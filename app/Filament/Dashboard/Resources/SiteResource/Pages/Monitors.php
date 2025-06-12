<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;

use App\Services\GripNotifications;

use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;

use App\Jobs\Tests\PageSpeed;

class Monitors extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.monitors';

    // public function render(): View
    // {
    //     return view('filament.sites.view-site');
    // }
    public function runLightHouseTest()
    {
        
    }

  

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }


}
