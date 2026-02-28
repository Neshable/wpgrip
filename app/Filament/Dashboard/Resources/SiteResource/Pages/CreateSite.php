<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Jobs\Site\TakeHomeScreenshot;
use App\Services\Plans\SubscriptionLimitChecker;
use Filament\Resources\Pages\CreateRecord;

class CreateSite extends CreateRecord
{
    protected static string $resource = SiteResource::class;

    protected function beforeCreate(): void
    {
        if (!SubscriptionLimitChecker::canCreate('site')) {
            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        // Queue a screenshot right after the site is saved
        TakeHomeScreenshot::dispatch($this->record);
    }
}
