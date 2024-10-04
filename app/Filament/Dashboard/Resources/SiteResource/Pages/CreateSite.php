<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

use App\Services\Plans\SubscriptionLimitChecker;
use Filament\Facades\Filament;

use App\Models\Site;


class CreateSite extends CreateRecord
{
    protected static string $resource = SiteResource::class;

    protected function beforeCreate(): void
    {
        if ( !SubscriptionLimitChecker::canCreate('site') ) 
        {
            $this->halt();
        }
     
    }

 
}
