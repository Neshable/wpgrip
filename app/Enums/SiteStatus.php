<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
 
enum SiteStatus: string implements HasLabel
{
    case UpdatingCore = 'updating_core';
    case UpdatingPlugin = 'updating_plugin';
    case CreatingBackup = 'backup_in_progress';
    case SecurityScan = 'security_scan';
    case Warning = 'warning';
    case Normal = 'normal';
    case Working = 'working';

    public function getLabel(): ?string
    {
        return $this->name;
        
        // or
    
        return match ($this) {
            self::UpdatingCore => 'Updating Core',
            self::UpdatingPlugin => 'Updating some plugins',
            self::CreatingBackup => 'Backup in progress',
            self::SecurityScan => 'Security check in progress',
            self::Working => 'Working',
        };
    }
}