<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
 
enum HostingProvider: string implements HasLabel, HasIcon, HasColor
{
    case Hostinger = 'hostinger';
    case SiteGround = 'siteground';
    case BlueHost = 'bluehost';
    case GoDaddy = 'godaddy';
    case DreamHost = 'dreamhost';
    case DigitalOcean = 'digitalocean';
    case GoogleCloud = 'googlecloud';
    case Vultr = 'vultr';
    case Linode = 'linode';
    case HetznerCloud = 'hetzner';
    case Azure = 'azure';
    case AWS = 'awsec2';
    case AWSLightsail = 'awslightsail';
    case Other = 'other';
    
    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Azure => 'info',
            self::Vultr => 'info',
            self::AWS => 'warning',
            self::Linode => 'info',
            self::GoogleCloud => 'danger',
            self::HetznerCloud => 'danger',
            self::DigitalOcean => 'info',
            default => 'gray'
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Azure => 'azure',
            self::Vultr => 'vultr',
            self::AWS => 'lightsail',
            self::Linode => 'akamai',
            self::GoogleCloud => 'google',
            self::HetznerCloud => 'hetzner',
            self::DigitalOcean => 'digitalocean',
            self::Hostinger => 'hostinger',
            self::GoDaddy => 'godaddy',
            default => 'ubuntu'
        };
    }
}