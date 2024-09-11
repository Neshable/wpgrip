<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;

use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Illuminate\Contracts\View\View;


class ShowBackups extends ViewRecord 
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.backups';

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    protected function getActions(): array
    {
        return [                    
            Actions\EditAction::make()
                ->label('Site Settings')
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
