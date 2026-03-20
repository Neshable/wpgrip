<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class TenancySettings extends Page
{
    protected string $view = 'filament.admin.pages.tenancy-settings';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->hasPermissionTo('update settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Tenancy');
    }
}
