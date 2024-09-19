<?php

namespace App\Filament\Dashboard\Resources\BackupResource\Pages;

use App\Filament\Dashboard\Resources\BackupResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBackup extends ViewRecord
{
    protected static string $resource = BackupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
