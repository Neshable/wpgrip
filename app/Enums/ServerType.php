<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
 
enum ServerType: string implements HasLabel
{
    case VPS = 'vps';
    case Cloud = 'cloud';
    case Dedicated = 'dedicated';
    case Shared = 'shared';
    case Other = 'other';
    
    public function getLabel(): ?string
    {
        return $this->name;
    }
}