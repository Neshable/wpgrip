<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClientSource: string implements HasLabel
{
    case Referral = 'referral';
    case Organic = 'organic';
    case Social = 'social';
    case Advertising = 'advertising';
    case ColdOutreach = 'cold_outreach';
    case Partnership = 'partnership';
    case Existing = 'existing';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Referral => 'Referral',
            self::Organic => 'Organic / Inbound',
            self::Social => 'Social Media',
            self::Advertising => 'Advertising',
            self::ColdOutreach => 'Cold Outreach',
            self::Partnership => 'Partnership',
            self::Existing => 'Existing Relationship',
            self::Other => 'Other',
        };
    }
}
