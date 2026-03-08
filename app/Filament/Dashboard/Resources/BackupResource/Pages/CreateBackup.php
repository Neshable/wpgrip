<?php

namespace App\Filament\Dashboard\Resources\BackupResource\Pages;

use App\Filament\Dashboard\Resources\BackupResource;
use App\Services\Plans\SubscriptionLimitChecker;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBackup extends CreateRecord
{
    protected static string $resource = BackupResource::class;

    protected function beforeCreate(): void
    {
        if (! SubscriptionLimitChecker::canCreate('backup')) {
            $this->halt();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type']      = 'db';
        $data['tenant_id'] = filament()->getTenant()->id;
        $data['status']    = 'active';
        return $data;
    }
}
