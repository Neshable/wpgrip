<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;

use Illuminate\Contracts\View\View;



class Blacklists extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.blacklists';


    public function getHeader(): ?View
    {
        return view('site.single.header');
    }


}
