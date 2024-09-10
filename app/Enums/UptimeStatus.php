<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Livewire\Wireable;

class UptimeStatus implements Wireable
{
    public const NOT_YET_CHECKED = 'not yet checked';
    public const UP = 'up';
    public const DOWN = 'down';

    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            static::NOT_YET_CHECKED => 'Not checked',
            static::UP => 'Up and running',
            static::DOWN => 'Down',
        };
    }

    public function toLivewire()
    {
        return [
            'NOT_YET_CHECKED' => self::NOT_YET_CHECKED,
            'UP' => self::UP,
            'DOWN' => self::DOWN,
        ];
    }
 
    public static function fromLivewire($value)
    {
   
        return [
            'NOT_YET_CHECKED' => self::NOT_YET_CHECKED,
            'UP' => self::UP,
            'DOWN' => self::DOWN,
        ];

    }
}
