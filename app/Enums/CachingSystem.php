<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
 
enum CachingSystem: string implements HasLabel
{
    case ObjectCache = 'object';
    case Beaver = 'beaver';
    case Autoptimize  = 'autoptimize';
    case CacheEnabler = 'cache_enabler';
    case FastestCache = 'fastest_cache';
    case W3TotalCache  = 'w3_total_cache';
    case WPRocket = 'wprocket';
    case SuperCache = 'supercache';
 


    public function getLabel(): ?string
    {
        return match ($this) {
            self::ObjectCache => 'Object cache ( Redis )',
            self::Beaver => 'Beaver theme cache',
            self::Autoptimize => 'Autoptimize plugin',
            self::CacheEnabler => 'Cache Enabler plugin',
            self::FastestCache => 'Fastest Cache plugin',
            self::W3TotalCache => 'W3 Total Cache',
            self::SuperCache => 'Super Cache',
            default => $this->name
        };
    }

    // public function getIcon(): ?string
    // {
    //     return match ($this) {
    //         self::Azure => 'azure',
    //         self::DigitalOcean => 'digitalocean',
    //         default => 'azure'
    //     };
    // }
}