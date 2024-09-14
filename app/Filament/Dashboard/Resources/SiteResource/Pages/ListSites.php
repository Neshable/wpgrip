<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Illuminate\Database\Eloquent\Builder;

class ListSites extends ListRecords
{
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
