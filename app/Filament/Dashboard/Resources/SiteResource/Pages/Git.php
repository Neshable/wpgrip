<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

use App\Models\Repository;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\View\View;



class Git extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected string $view = 'site.single.repos';

    // public function render(): View
    // {
    //     return view('filament.sites.view-site');
    // }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }
    

    public function getStagingSite(): StagingSite|bool
    {
        // $staging_site = StagingSite::where('site_id', $this->record->id )->first();
        
        // if ( $staging_site )
        // {
        //     return $staging_site;
        // }

        // return false;
    }

    public function createNewRepo()
    {
        CreateAction::make()
            ->model(Repository::class)
            ->form([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                // ...
            ]);
        // @todo change to dispatch to longrunning queue.
        // SyncDatabase::dispatchSync( $this->getStagingSite() );
    }

}
