<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Services\Plans\SubscriptionLimitChecker;
use App\Services\ShelleyManager;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Contracts\View\View;

class Insights extends ViewRecord implements HasActions
{
    protected static string $resource = SiteResource::class;

    protected string $view = 'site.single.insights';

    /** Port the Shelley instance for this site is listening on (0 = not started) */
    public int $shelleyPort = 0;

    /** Human-readable error if Shelley failed to start */
    public string $shelleyError = '';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (! SubscriptionLimitChecker::canUseAi()) {
            $this->redirect(static::getResource()::getUrl('view', [
                'record' => $record,
                'tenant' => \Filament\Facades\Filament::getTenant(),
            ]));
            return;
        }

        try {
            $manager = app(ShelleyManager::class);
            $this->shelleyPort = $manager->ensureRunning($this->record);
        } catch (\Throwable $e) {
            $this->shelleyError = $e->getMessage();
            \Illuminate\Support\Facades\Log::error('Shelley failed to start: ' . $e->getMessage());
        }
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }
}
