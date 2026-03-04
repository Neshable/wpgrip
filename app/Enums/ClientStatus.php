<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum ClientStatus: string implements HasLabel, HasColor, HasIcon
{
    case Lead = 'lead';
    case Active = 'active';
    case Inactive = 'inactive';
    case Churned = 'churned';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Lead => 'Lead',
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Churned => 'Churned',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Lead => 'info',
            self::Active => 'success',
            self::Inactive => 'warning',
            self::Churned => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Lead => 'heroicon-m-sparkles',
            self::Active => 'heroicon-m-check-circle',
            self::Inactive => 'heroicon-m-pause-circle',
            self::Churned => 'heroicon-m-x-circle',
        };
    }
}
