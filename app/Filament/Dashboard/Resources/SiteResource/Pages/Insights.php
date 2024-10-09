<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;

use App\Jobs\ListAllWPPlugins;
use App\Jobs\Site\GetAllPlugins;
use App\Jobs;


use Filament\Notifications\Notification; 

use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\View as ViewComponent;
use Filament\Forms\Components\Section;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ViewField;

use Illuminate\Support\HtmlString;
use Filament\Forms;

use Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Actions\Contracts\HasActions;

class Insights extends ViewRecord implements HasActions
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.insights';

    protected function getActions(): array
    {
        return [
            Action::make('listallplugins')
                ->label('Sync Plugin List')
                ->action(function() {
                    ListAllWPPlugins::dispatch( $this->record );
                    // Push notification.
                    Notification::make()
                        ->title('Sync Started.')
                        ->success()
                        ->send();
                })
        ];
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }
  
    public function checkStatusCode()
    {
        return Action::make('delete')
        ->requiresConfirmation()
        ->action(fn () => 'ss');
        
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

}
