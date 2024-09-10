<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Livewire\Wireable;

class CertificateStatus implements Wireable
{
    public const NOT_YET_CHECKED = 'not yet checked';
    public const VALID = 'valid';
    public const INVALID = 'invalid';

    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            static::NOT_YET_CHECKED => 'Not checked',
            static::VALID => 'Valid SSL',
            static::INVALID => 'Invalid SSL',
        };
    }

    public function toLivewire()
    {
        return [
            'NOT_YET_CHECKED' => self::NOT_YET_CHECKED,
            'VALID' => self::VALID,
            'INVALID' => self::INVALID,
        ];
    }
 
    public static function fromLivewire($value)
    {
   
        return [
            'NOT_YET_CHECKED' => self::NOT_YET_CHECKED,
            'VALID' => self::VALID,
            'INVALID' => self::INVALID,
        ];

    }
}

