<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

use App\Services\GripNotifications;
use Filament\Notifications\Notification; 

use App\Jobs\Staging\SyncDatabase;
use App\Jobs\Staging\SyncFiles;
use App\Jobs\Staging\SyncFinalTweaks;

use Filament\Actions\Action;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;

use Filament\Forms\Components;

use Illuminate\Support\HtmlString;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use App\Models\Site;
use App\Models\Backup;

use App\Jobs\Staging\SyncLiveToStaging;
use App\Jobs\Backup\Database\RemoteDBRestore;

use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;


class Staging extends ViewRecord implements HasForms, HasActions
{
    
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.staging';

    // public function render(): View
    // {
    //     return view('filament.sites.view-site');
    // }

    public function getStagingSite(): Site|bool
    {
        // @todo return all and loop/table/livewire.
        $staging_site = $this->record->children()->first();
        
        if ( $staging_site )
        {
            return $staging_site;
        }

        return false;
    }

    
    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    private function getStagingID()
    {
        return optional( $this->record->children()->first() )->id;
    }

    public function syncLiveToStaging()
    {
        return Action::make('stagingsync')
        ->requiresConfirmation()
        ->color('info')
        ->label('Sync Staging')
        ->modalHeading('Syncing the staging')
        ->modalSubmitActionLabel('Sync')
        ->modalIcon('heroicon-o-arrow-path')
        ->modalDescription('Please select a backup from the production site to restore on the staging site. You can select a database backup, files backup or both.')
        ->successNotificationTitle( 'Would you like to clear the cache?' )  
        ->form([
            Components\Select::make('db')
                ->label('Database backup to sync')
                ->options( 
                    Backup::query()
                    ->where('site_id', $this->record->id )
                    ->where('type', 'db')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item['id'] => 'Created ' . $item['created_at']];
                    })
                    ->toArray()
                ), 
            Components\Select::make('files')
                ->label('Files backup to sync')
                ->options( 
                    Backup::query()
                    ->where('site_id', $this->record->id )
                    ->where('type', 'files')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item['id'] => 'Created ' . $item['created_at']];
                    })
                    ->toArray()
                ),
        ])
        ->action(function (array $data): void {
            // array:2 [▼ // app/Filament/App/Resources/SiteResource/Pages/Staging.php:108
            // "db" => "56"
            // "files" => null
            // ]
            
            // Restore the DB to the 
            RemoteDBRestore::dispatch( $this->getStagingID(), $data['db'] );
            // Sent notification.
            GripNotifications::getStagingSyncDispatched();

            // This imports the database and deletes it afterwards.
            // SyncFinalTweaks::dispatchSync( $this->record );



            // $batch = Bus::batch([
            //     [
            //         new SyncDatabase($this->record),
            //     ],
            //     [
            //         new SyncFiles($this->record),
            //     ],
            //     [
            //         new SyncFinalTweaks($this->record),
            //     ]
            // ])->then(function (Batch $batch) {
               
            // })->catch(function (Batch $batch, Throwable $e) {
            //     // Somet catch.
            // })->dispatch();

            // $this->record->sitemeta->batch_id = $batch->id;
            // $this->record->sitemeta->save();

            
            

        });


        // if (  $this->record->children() )
        // {
        //    SyncFinalTweaks::dispatchSync( $this->record );

        //     $batch = Bus::batch([
        //         [
        //             new SyncDatabase($this->record),
        //         ],
        //         [
        //             new SyncFiles($this->record),
        //         ],
        //         [
        //             new SyncFinalTweaks($this->record),
        //         ]
        //     ])->then(function (Batch $batch) {
               
        //     })->catch(function (Batch $batch, Throwable $e) {
        //         // Somet catch.
        //     })->dispatch();

        //     $this->record->sitemeta->batch_id = $batch->id;
        //     $this->record->sitemeta->save();

            
        //     GripNotifications::getStagingSyncDispatched();
  
        // }
        
    }



    // public function getHeader(): ?View
    // {
    //     return view('site.single.header');
    // }

}
