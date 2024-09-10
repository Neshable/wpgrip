<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
 
enum BoardType: string implements HasLabel, HasIcon
{
    case Trello = 'Trello';
    case Notion = 'Notion';
    case Jira = 'Jira';
    case Asana = 'Asana';
    case Monday = 'Monday';
    case Clickup = 'Clickup';
    case Other = 'other';
    
    public function getLabel(): ?string
    {
        return $this->name;
    }

    // @todo add icons.
    public function getIcon(): ?string
    {
        return match ($this) {
            self::Trello => 'trello',
            self::Notion => 'trello',
            default => 'trello'
        };
    }
}