<?php

namespace App\Filament\Dashboard\Pages;

use App\Constants\TenancyPermissionConstants;
use App\Services\TenantPermissionManager;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

 
class Dashboard extends \Filament\Pages\Dashboard
{
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    public function getColumns(): int | string | array
    {
        return 3;
    }

    public function getTitle(): string | Htmlable
    {
        return Filament::getTenant()->name . ' workspace';
    }
}