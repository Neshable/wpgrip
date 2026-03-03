<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Jobs\Site\TakeHomeScreenshot;
use App\Services\Plans\SubscriptionLimitChecker;
use App\Services\ActivityLogger;
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
        TakeHomeScreenshot::dispatch($this->record);
        ActivityLogger::siteAction('site.created', $this->record, [
            'url' => $this->record->url,
        ]);
    }
}
