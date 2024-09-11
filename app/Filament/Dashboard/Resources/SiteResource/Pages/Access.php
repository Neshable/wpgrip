<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;

use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;

use App\Services\GripNotifications;

use Illuminate\Support\Facades\Http;

use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification; 



class Access extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.access';


    public function checkStatusCode()
    {
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
