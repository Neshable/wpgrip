<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class GeneralSettings extends Page
{
    protected string $view = 'filament.admin.pages.general-settings';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->hasPermissionTo('update settings');
    }
}
