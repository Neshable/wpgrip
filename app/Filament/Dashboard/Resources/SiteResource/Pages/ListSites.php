<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Dashboard\Resources\SiteResource\Widgets\SitesOverview;

use Illuminate\Database\Eloquent\Builder;

class ListSites extends ListRecords
{
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->outlined()
            // ->hidden( UptimeMonitor::query()->where('site_id', $this->site_id )->count() > 3 )
            ->label('Add a site'),
            // Actions\CreateAction::make()
            // ->disabled()
            // ->badge('Pro plan')
            // ->outlined()
            // // ->hidden( UptimeMonitor::query()->where('site_id', $this->site_id )->count() > 3 )
            // ->label('Add a site'),
        ];
    }

    protected function getWidgets(): array
    {
        return [
            //SitesOverview::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            //SitesOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'Production Sites' => \Filament\Resources\Components\Tab::make()
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('is_staging', false)),
            'Staging Sites' => \Filament\Resources\Components\Tab::make()
                        ->modifyQueryUsing(fn (Builder $query) => $query->where('is_staging', true)),
        ];
    }
}
