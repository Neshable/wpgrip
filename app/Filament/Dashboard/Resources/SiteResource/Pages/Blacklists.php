<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

use Illuminate\Contracts\View\View;



class Blacklists extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected string $view = 'site.single.blacklists';


    public function getHeader(): ?View
    {
        return view('site.single.header');
    }


}
