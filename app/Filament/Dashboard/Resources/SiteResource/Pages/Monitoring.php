<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\View\View;

use App\Filament\Dashboard\Resources\SiteResource\Widgets\ResponseTimeChart;
use App\Filament\Dashboard\Resources\SiteResource\Widgets\UptimeChart;

class Monitoring extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.monitoring';

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    /**
     * Check uptime only for this site's monitors (not all monitors globally).
     */
    public function checkUptime(): void
    {
        $monitor = $this->getRecord()->get_main_monitor();

        if ($monitor) {
            Artisan::call('monitor:check-uptime', [
                '--url' => (string) $monitor->url,
            ]);
        }
    }

    /**
     * Check SSL certificate for this site's main URL only.
     */
    public function checkSSL(): void
    {
        // Certificate check doesn't support --url filtering,
        // but it's fast and only checks enabled monitors.
        Artisan::call('monitor:check-certificate');
    }

    protected function getWidgets(): array
    {
        return [
            UptimeChart::class,
            ResponseTimeChart::class,
        ];
    }
}
