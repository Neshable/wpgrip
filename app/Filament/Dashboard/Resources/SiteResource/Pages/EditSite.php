<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Services\ActivityLogger;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSite extends EditRecord
{
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => ActivityLogger::siteAction('site.deleted', $this->record)),
        ];
    }

    protected function afterSave(): void
    {
        ActivityLogger::siteAction('site.updated', $this->record, [
            'url' => $this->record->url,
        ]);
    }
}
