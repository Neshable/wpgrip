<?php

namespace App\Livewire;
 
use App\Models\Site;
use App\Models\VRT;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ViewColumn;

use App\Jobs\Site\CompareImages;

use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;
 
class ListScreenshots extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $site_id = 8899;
    
    public function table(Table $table): Table
    {   
        return $table
            ->query( VRT::query()->where('site_id', $this->site_id )->orderBy('created_at', 'desc') )
            ->columns([
                // ImageColumn::make('file_path')->disk('public')
                // ->label('Screenshot')
                // ->visibility('private')
                // ->extraImgAttributes(['loading' => 'lazy'])
                // ->width(260)
                // ->height(150),

                Tables\Columns\Layout\Stack::make([
                    ViewColumn::make('info')->view('filament.tables.columns.screenshot.info'),
                ]),
      
            ])
            // ->deferLoading()
            ->filters([
                // ...
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Action::make('Compare')
                        ->action(function ( VRT $screenshot ) {
                            $site_model = Site::FindOrFail( $this->site_id );
                            if ( $site_model )
                            {
                                CompareImages::dispatch( $screenshot, $site_model );
                                Notification::make()->title('Comparison queued.')->success()->body('Image comparison is running in the background.')->send();
                            }
                        } )
                        ->label('Run manual test')
                        ->tooltip('Compare this to the control screenshot.'),
                // Tables\Actions\ActionGroup::make([
                //     Tables\Actions\DeleteAction::make(),
                    
                // ]),
            ])
            ->headerActions([
                // Action::make('create')
                //         ->requiresConfirmation()
                //         ->label('Backup DB Manually')
                //         ->modalHeading('Creating a new manual backup')
                //         ->modalSubmitActionLabel('Create')
                //         ->color('warning')
                //         ->icon('heroicon-o-server-stack')
                //         ->modalIcon('heroicon-o-server-stack')
                //         ->action(function () {
                //             $record = Site::find( $this->site_id );
                //             RemoteDBBackup::dispatch( $record, 'manual' )->onQueue('longrunning');
                //             Notification::make()
                //                 ->title('Backup process started.')
                //                 ->success()
                //                 ->body('The process will finish in the background. A notification will appear once it\'s completed.') 
                //                 ->send();
                //         } ),
                // Action::make('create-files')
                //     ->requiresConfirmation()
                //     ->label('Backup Files Manually')
                //     ->modalHeading('Creating a new manual files backup')
                //     ->modalSubmitActionLabel('Backup Files')
                //     ->color('info')
                //     ->icon('heroicon-o-server-stack')
                //     ->modalIcon('heroicon-o-server-stack')
                //     ->action(function () {
                //         $record = Site::find( $this->site_id );
                //         // @todo change to dispatchSync.
                //         RemoteFilesBackup::dispatch( $record, 'manual' )->onQueue('longrunning');
                //         Notification::make()
                //             ->title('Backup process started.')
                //             ->success()
                //             ->body('The process will finish in the background. A notification will appear once it\'s completed.') 
                //             ->send();
                //     } )
            ])
            ->emptyStateHeading('No screenshots found')
            ->emptyStateDescription('You haven\'t created any control screenshots yet.')
            ->heading('Test screenshots')
            ->description('List of all recent visual tests. Current retention period is 14 days.')
            ->defaultPaginationPageOption('all')
            ->paginated(false)
            
            ->bulkActions([
              
            ]);
            // ->contentGrid([
            //     'md' => 2,
            //     'xl' => 3,
            // ]);
    }

    public function getSiteModel()
    {
        if ( $this->site_id )
        {
            return Site::FindOrfail( $this->site_id );
        }
    }

    
    public function render(): View
    {
        return view('livewire.list-screenshots'); 
    }
}