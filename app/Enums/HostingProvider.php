<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
 
enum HostingProvider: string implements HasLabel,HasIcon
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

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Azure => 'azure',
            self::DigitalOcean => 'digitalocean',
            default => 'azure'
        };
    }
}