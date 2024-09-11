<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;


use App\Jobs\SyncSite;
use App\Jobs\CheckSSLExpiry;
use App\Jobs\ListAllWPPlugins;
use App\Jobs\RemoteDBBackup;
use App\Jobs\CheckDBStructure;
use App\Jobs;

use Filament\Notifications\Notification; 
use Illuminate\Support\Facades\Artisan;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;



class Monitoring extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.monitoring';

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    public function checkUptime()
    {
         // Run the Artisan command
         Artisan::call('monitor:check-uptime');
    }


    public function checkSSL()
    {
        Artisan::call('monitor:check-certificate');

        // Optionally, you can capture the output of the command
        $output = Artisan::output();
        if ( $output )
        {
            // send notification.
        }
 
    }


    // public function render(): View
    // {
    //     return view('filament.sites.view-site');
    // }

}
