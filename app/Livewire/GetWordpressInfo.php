<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

use App\Models\Site;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

use App\Enums\SiteStatus;
use App\Jobs\Updates\UpdateWPCore;
use App\Jobs\Site\SyncSiteStats;

class GetWordpressInfo extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $site;
    public $loading = true;

    /** Latest WP version from API */
    public ?string $latestVersion = null;

    /** All available versions from API */
    public array $allVersions = [];

    /** Whether the API call succeeded */
    public bool $apiError = false;

    public function mount()
    {
        $this->fetchWPApi();
    }

    public function fetchWPApi(): void
    {
        try {
            $response = Http::timeout(8)->get('https://api.wordpress.org/core/version-check/1.7/');

            if ($response->ok()) {
                $data = $response->json();
                $this->latestVersion = $data['offers'][0]['current'] ?? null;

                // Build unique version list
                $seen = [];
                foreach ($data['offers'] as $offer) {
                    $v = $offer['version'];
                    if (!isset($seen[$v])) {
                        $seen[$v] = [
                            'version'       => $v,
                            'response'      => $offer['response'],
                            'php_version'   => $offer['php_version'],
                            'mysql_version' => $offer['mysql_version'],
                            'download'      => $offer['download'],
                        ];
                    }
                }
                $this->allVersions = array_values($seen);
            } else {
                $this->apiError = true;
            }
        } catch (\Throwable $e) {
            $this->apiError = true;
        }

        $this->loading = false;
    }

    public function updateAction(): Action
    {
        return Action::make('update')
            ->label('Update to ' . ($this->latestVersion ?? 'latest'))
            ->requiresConfirmation()
            ->color('success')
            ->icon('heroicon-o-arrow-up-circle')
            ->modalHeading('Update WordPress Core')
            ->modalDescription('This will update WordPress to the latest version. A backup is recommended first.')
            ->modalSubmitActionLabel('Update now')
            ->action(function (array $arguments): void {
                $site = Site::find($arguments['site_id']);
                $site->status = SiteStatus::UpdatingCore;
                $site->save();
                UpdateWPCore::dispatch($site, $this->latestVersion);

                Notification::make()
                    ->title('Update dispatched')
                    ->body('WordPress update to ' . $this->latestVersion . ' has been queued.')
                    ->success()
                    ->send();
            });
    }

    public function rollbackAction(): Action
    {
        $options = [];
        foreach ($this->allVersions as $v) {
            if ($v['version'] !== (string)$this->site->wp_ver) {
                $label = $v['version'];
                if ($v['version'] === $this->latestVersion) {
                    $label .= ' (latest)';
                }
                $label .= '  — PHP ' . $v['php_version'] . '+';
                $options[$v['version']] = $label;
            }
        }

        return Action::make('rollback')
            ->label('Switch version')
            ->requiresConfirmation()
            ->color('warning')
            ->icon('heroicon-o-arrow-path')
            ->form([
                Select::make('version')
                    ->label('Target WordPress Version')
                    ->options($options)
                    ->required()
                    ->searchable(),
            ])
            ->modalHeading('Switch WordPress Core Version')
            ->modalDescription('You can upgrade or rollback. Ensure plugins & themes are compatible with the target version.')
            ->modalSubmitActionLabel('Switch now')
            ->action(function (array $data, array $arguments): void {
                $site = Site::find($arguments['site_id']);
                $isDowngrade = version_compare($data['version'], (string)$site->wp_ver, '<');

                $site->status = SiteStatus::UpdatingCore;
                $site->save();

                UpdateWPCore::dispatch($site, $data['version'], $isDowngrade);

                Notification::make()
                    ->title($isDowngrade ? 'Rollback dispatched' : 'Update dispatched')
                    ->body('WordPress version switch to ' . $data['version'] . ' has been queued.')
                    ->warning()
                    ->send();
            });
    }

    public function syncVersionAction(): Action
    {
        return Action::make('syncVersion')
            ->label('Re-sync stats')
            ->color('gray')
            ->icon('heroicon-o-arrow-path')
            ->requiresConfirmation()
            ->modalHeading('Re-sync site stats')
            ->modalDescription('Connect via SSH and pull the current WordPress version and site stats.')
            ->modalSubmitActionLabel('Sync now')
            ->action(function (array $arguments): void {
                $site = Site::find($arguments['site_id']);
                SyncSiteStats::dispatchSync($site);
                $this->site->refresh();

                Notification::make()
                    ->title('Stats synced')
                    ->success()
                    ->send();
            });
    }

    #[On('site-updated')]
    public function refresh(): void
    {
        $this->site->refresh();
    }

    public function render()
    {
        return view('livewire.get-wordpress-info');
    }
}
