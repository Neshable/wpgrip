<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;


use App\Jobs\SyncSite;
use App\Jobs\CheckSSLExpiry;
use App\Jobs\Site\GetAllPlugins;

use App\Jobs\RemoteDBBackup;
use App\Jobs\CheckDBStructure;
use App\Jobs\Site\TakeHomeScreenshot;
use App\Jobs\Site\SyncSiteStats;
use App\Jobs;

use Filament\Notifications\Notification; 

use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Fieldset;


// use Filament\Actions\Action;

use Illuminate\Contracts\Support\Htmlable;

use Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\StatsOverview;


class ViewSite extends ViewRecord
{
    protected static string $resource = SiteResource::class;
    
    // protected static ?string $title = 'WWW';

    protected static string $view = 'site.single.overview';

    // public function render(): View
    // {
    //     return view('filament.sites.view-site');
    // }

    public function getTitle(): string | Htmlable
    {
        return $this->getRecord()->name;
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }


    protected function getHeaderWidgets(): array
    {
        return [
            // StatsOverview::class
        ];
    }


    public function getFormSchema(): array
    {
 

        return [    
            Section::make('Summary')
                ->description('Summary')
                ->schema([
                    Fieldset::make('Label')
                    ->schema([
                        // ...
                    ])
                    ->columns(3)
                ])                    
        ];
        
    
    }

    protected function getActions(): array
    {
        return [       
            Actions\EditAction::make()
                ->label('Site settings'),
        ];
    }

    // protected function getTableColumns(): array
    // {
    //     return ['name'];
    // }

    // protected function getTableQuery(): Builder
    // {
    //      return Product::query()->where('id',1);
    // }

    public function triggerSync()
    {

        SyncSiteStats::dispatch($this->record);

        return Notification::make()
                ->title('Syncing...')
                ->success()
                ->body('Syncing website...') 
                ->send();
    }

    public function triggerScreenshot()
    {
        TakeHomeScreenshot::dispatchSync($this->record);

        return Notification::make()
                ->title('New screenshot taken.')
                ->success()
                ->send();
    }
    
    public function triggerAction( string $action = 'default' ) 
    {
        switch ( $action ) {
            case 'backup':
                RemoteDBBackup::dispatch( $this->record );
                 // Save to DB and push notification.
                 Notification::make()
                    ->title('Backup started.')
                    ->success()
                    ->body('Backup job successfully started in the background.') 
                    ->send();
                break;
            case 'backupfiles':
                Jobs\RemoteFilesBackup::dispatch( $this->record );
                    // Save to DB and push notification.
                    Notification::make()
                    ->title('Backup started.')
                    ->success()
                    ->body('Backup job successfully started in the background.') 
                    ->send();
                break;
            case 'structure':
                // $test = Action::make('sync')
                //     ->action(function ( Site $record) {
                //         SyncSite::dispatch($record);
                //         Notification::make()
                //             ->title('Sync started successfully')
                //             ->success()
                //             ->body('The process will finish in the background. A notification will appear once it\'s completed.') 
                //             ->send();
                //     } )
                //     ->icon('heroicon-o-check-circle')
                //     ->color('success')
                //     ->requiresConfirmation()
                //     ->modalHeading('Sync Website?')
                //     ->modalSubheading('Are you sure you\'d like to sync this website?')
                //     ->modalButton('Yes, sync now')
                //     ->tooltip('Sync this site');
                // dd($test);
                CheckDBStructure::dispatch( $this->record );
                break;
            case 'vrt':
                Jobs\MakeScreenshot::dispatch( $this->record );
                break;
            default:
        }
        
    }
}
