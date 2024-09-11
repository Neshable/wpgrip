<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;

use Filament\Resources\Pages\ViewRecord;
// use Filament\Pages\Actions\Action;

use App\Services\GripNotifications;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;

use Illuminate\Support\Facades\Http;
use App\Enums\CachingSystem;
use App\Jobs\Site\CustomCLICommand;
use App\Services\WPCliService;

use App\Jobs\External\GetBlacklistMonitors;

use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification; 
use Filament\Actions\Action;
use Filament\Forms\Components;


class Tools extends ViewRecord 
{ 

    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.tools';

    public function clearCache()
    {
        return Action::make('clearCache')
        ->requiresConfirmation()
        ->color('info')
        ->label('Clear cache')
        ->modalHeading('Clearing a cache')
        ->modalSubmitActionLabel('Clear cache')
        ->modalIcon('heroicon-o-arrow-path')
        ->modalDescription('Are you sure you\'d like to clear this cache? ')
        ->successNotificationTitle( 'Would you like to clear the cache?' )   
        ->action( fn () => CustomCLICommand::dispatchSync( $this->record, WPCliService::clearCache('wprocket') ) );
    }

    public function clearOtherCache()
    {
        return Action::make('clearCache')
        ->requiresConfirmation()
        ->color('info')
        ->label('Clear cache')
        ->modalHeading('Clearing a cache')
        ->modalSubmitActionLabel('Clear cache')
        ->modalIcon('heroicon-o-arrow-path')
        ->modalDescription('Are you sure you\'d like to clear this cache? ')
        ->successNotificationTitle( 'Would you like to clear the cache?' )  
        ->form([
            Components\Select::make('caching')
                ->label('Caching system')
                ->options(CachingSystem::class)
                ->required(),
        ])
        ->action(function (array $data): void {
            dd($data);
        });
    }

    public function checkStatusCode()
    {
        // GetBlacklistMonitors::dispatchSync( $this->record );
        // return Action::make('delete')
        // ->requiresConfirmation()
        // ->action(fn () => 'ss');
        
        $response = Http::get( $this->record->url );

        GripNotifications::getStatusCode( $response->status() );

        if ( $response->failed() )
        {
            // $response->badRequest() // 400 Bad Request
            //$response->forbidden();  // 403 forbidden
            //$response->tooManyRequests(); // 429 Too Many Requests
            //$response->serverError(); // 500 Internal Server Error
        }
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    // public function render(): View
    // {
    //     return view('filament.sites.view-site');
    // }

}
