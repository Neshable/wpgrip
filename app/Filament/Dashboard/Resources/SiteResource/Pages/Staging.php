<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Jobs\Staging\SyncProductionToStaging;
use App\Models\Site;
use App\Models\StagingSync;
use App\Services\GripNotifications;
use App\Services\Plans\SubscriptionLimitChecker;
use Filament\Actions\Action;
use Filament\Forms\Components;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class Staging extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.staging';

    public function getStagingSite(): Site|bool
    {
        $staging = $this->record->children()->first();

        return $staging ?: false;
    }

    public function getLatestSync(): ?StagingSync
    {
        $staging = $this->getStagingSite();

        if (! $staging) {
            return null;
        }

        return StagingSync::latestForStaging($staging->id)->first();
    }

    public function canSync(): bool
    {
        return SubscriptionLimitChecker::canUseStagingSyncSilent();
    }

    public function isSyncRunning(): bool
    {
        $latest = $this->getLatestSync();

        return $latest && $latest->isRunning();
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    public function syncLiveToStaging(): Action
    {
        return Action::make('stagingsync')
            ->requiresConfirmation()
            ->color('primary')
            ->label('Pull from Production')
            ->icon('heroicon-o-arrow-down-tray')
            ->modalHeading('Sync from Production')
            ->modalSubmitActionLabel('Start Sync')
            ->modalIcon('heroicon-o-arrow-path')
            ->modalDescription('This will overwrite the staging site\'s database and/or uploads with data from the production site. Domains will be search-replaced automatically.')
            ->visible(fn () => $this->getStagingSite() && $this->canSync() && ! $this->isSyncRunning())
            ->form([
                Components\Toggle::make('sync_db')
                    ->label('Sync database')
                    ->helperText('Export production DB, import to staging, and search-replace domains.')
                    ->default(true),
                Components\Toggle::make('sync_uploads')
                    ->label('Sync uploads')
                    ->helperText('Copy wp-content/uploads from production to staging.')
                    ->default(true),
            ])
            ->action(function (array $data): void {
                if (! SubscriptionLimitChecker::canUseStagingSync()) {
                    return;
                }

                $staging = $this->record->children()->first();

                if (! $staging) {
                    GripNotifications::getCustomFailure('No staging site found.');
                    return;
                }

                if (! $data['sync_db'] && ! $data['sync_uploads']) {
                    GripNotifications::getCustomFailure('Please select at least one option to sync.');
                    return;
                }

                // Check if a sync is already running
                $running = StagingSync::where('staging_site_id', $staging->id)
                    ->whereNotIn('status', ['completed', 'failed'])
                    ->exists();

                if ($running) {
                    GripNotifications::getCustomFailure('A sync is already in progress for this staging site.');
                    return;
                }

                // Create sync record
                $syncRecord = StagingSync::create([
                    'production_site_id' => $this->record->id,
                    'staging_site_id' => $staging->id,
                    'tenant_id' => $this->record->tenant_id,
                    'status' => 'pending',
                    'sync_db' => $data['sync_db'],
                    'sync_uploads' => $data['sync_uploads'],
                ]);

                // Dispatch the job
                SyncProductionToStaging::dispatch(
                    $this->record,
                    $staging,
                    $syncRecord,
                    $data['sync_db'],
                    $data['sync_uploads'],
                );

                GripNotifications::getStagingSyncDispatched();
            });
    }
}
