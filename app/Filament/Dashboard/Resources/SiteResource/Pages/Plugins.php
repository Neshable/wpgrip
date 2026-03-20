<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

use App\Jobs\ListAllWPPlugins;
use App\Jobs\Site\GetAllPlugins;
use App\Jobs;


use Filament\Notifications\Notification; 

use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\View as ViewComponent;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ViewField;

use Illuminate\Support\HtmlString;
use Filament\Forms;

use Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Actions\Contracts\HasActions;

class Plugins extends ViewRecord implements HasActions
{
    protected static string $resource = SiteResource::class;

    protected string $view = 'site.single.plugins';

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
  
    public function safeUpdateAllPlugins()
    {
        return Action::make('safe')
            ->requiresConfirmation()
            ->action(fn () => 23);

    }

    public function getAllPlugins()
    {
        GetAllPlugins::dispatch( $this->record );
        Notification::make()
            ->title('Plugin sync queued.')
            ->success()
            ->body('Plugin list is being synced in the background.')
            ->send();
    }

    // public function render(): View
    // {
    //     return view('site.single.plugins');
    // }

}
