<?php

namespace App\Livewire;
 
use App\Models\Site;
use App\Models\Backup;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;

use App\Jobs\RemoteDBBackup;
use App\Jobs\Backup\Database\ManualRemoteDBBackup;
use App\Jobs\Backup\RemoteFilesBackup;
use Filament\Forms\Components\Select;

use Filament\Facades\Filament;

use Filament\Tables\Columns\ViewColumn;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

use App\Jobs\Backup\CopyFromRemote;
use App\Jobs\Backup\CreateArchive;
use App\Jobs\Backup\SendToS3;
use App\Jobs\Backup\DeleteAfterBackup;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;
use Carbon\Carbon;

use App\Services\GripNotifications;
 
class ListBackups extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $site_id = 8899;
    
    public function table(Table $table): Table
    {   
        return $table
            ->query( Backup::query()->where('site_id', $this->site_id ) )
            ->deferLoading()
            ->columns([
                ViewColumn::make('status')->view('filament.tables.columns.backup-status'),
                TextColumn::make('created_at'),
                // TextColumn::make('file_path'),
                // TextColumn::make('site.name')->sortable(),
                TextColumn::make('provider')->label('Storage'),
                TextColumn::make('type'),
                TextColumn::make('frequency'),
                TextColumn::make('db_size')
                    ->label('Size (MB)')
                    ->getStateUsing(function (Backup $record) {
                        return $record->getFormatedDBSize();
                    })
                    ->color('primary'),
                TextColumn::make('delete_date')->label('Expires on'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('restore')
                        ->requiresConfirmation()
                        ->label('Restore Backup')
                        ->modalHeading('Process of restoring a backup')
                        ->modalSubmitActionLabel('Restore')
                        ->successNotificationTitle('Are you sure you want to restore this backup?')
                        ->color('warning')
                        ->icon('heroicon-o-server-stack')
                        ->modalIcon('heroicon-o-server-stack')
                        ->action(fn (Backup $record) => $record->delete()),
                    Action::make('download')
                        ->color('info')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action( function(Backup $record) {
                            if( Storage::disk('s3')->exists( $record->file_path ) )
                            { 
                                return Storage::disk('s3')->download( $record->file_path );  

                                // $asset = Asset::find($id);
                                // $assetPath = Storage::disk('s3')->url($record->file_path);
                        
                                // header("Cache-Control: public");
                                // header("Content-Description: File Transfer");
                                // header("Content-Disposition: attachment; filename=" . basename($assetPath));
                                // header("Content-Type: " . $asset->mime);
                        
                                // return readfile($assetPath);
                            }
                            return false;
                    
                        }),
                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->headerActions([
                
                Action::make('create')
                        ->requiresConfirmation()
                        ->label('Backup DB Manually')
                        ->modalHeading('Creating a new manual backup')
                        ->modalSubmitActionLabel('Create')
                        ->color('warning')
                        ->icon('heroicon-o-server-stack')
                        ->modalIcon('heroicon-o-server-stack')
                        ->action(function () {
                            // Set the site model.
                            $record = Site::find( $this->site_id );
                            ManualRemoteDBBackup::dispatchSync( $record, 'manual' );
                            // RemoteDBBackup::dispatch( $record, 'manual' )->onQueue('longrunning');
                            Notification::make()
                                ->title('Backup process started.')
                                ->success()
                                ->body('The process will finish in the background. A notification will appear once it\'s completed.') 
                                ->send();
                        } ),

                // Action::make('create')
                //         ->form([
                //             Select::make('authorId')
                //                 ->label('Author')
                //                 ->options(['dummy', 'other'])
                //                 ->required(),
                //         ])
                //         ->requiresConfirmation()
                //         ->label('Backup DB Manually')
                //         ->modalHeading('Creating a new manual backup')
                //         ->modalSubmitActionLabel('Create')
                //         ->color('warning')
                //         ->icon('heroicon-o-server-stack')
                //         ->modalIcon('heroicon-o-server-stack')
                //         ->action(function (array $data): void {
                //             dd($data);
                //             // $record->author()->associate($data['authorId']);
                //             // $record->save();
                //             Notification::make()
                //                 ->title('Backup process started.')
                //                 ->success()
                //                 ->body('The process will finish in the background. A notification will appear once it\'s completed.') 
                //                 ->send();
                //         }),
                        

                       
                

                Action::make('create-files')
                    ->requiresConfirmation()
                    ->label('Backup Files Manually')
                    ->modalHeading('Creating a new manual files backup')
                    ->modalSubmitActionLabel('Backup Files')
                    ->color('info')
                    ->icon('heroicon-o-server-stack')
                    ->modalIcon('heroicon-o-server-stack')
                    ->action(function () {
                        $record = Site::find( $this->site_id );
                        $timestamp = Carbon::now()->format('YmdHi');
        
                        Bus::chain([
                            // Rsync from remote first.
                            // @todo maybe separate files into wp-content and rest
                            new CopyFromRemote( $record, $timestamp ),
                            // Create archive.
                            new CreateArchive( $record, $timestamp ),
                            // Push to S3.
                            new SendToS3( $record, $timestamp ),
                            // Delete all traces.
                            new DeleteAfterBackup( $record, $timestamp )
                        ])->catch(function (Throwable $e) {
                            //  First batch job failure detected
                            GripNotifications::getBackupFail( $record->user_id );
                        })->onQueue('longrunning')->dispatch();
               
                        // RemoteFilesBackup::dispatchSync( $record, 'manual' );
                        Notification::make()
                            ->title('Backup process started.')
                            ->success()
                            ->body('The process will finish in the background. A notification will appear once it\'s completed.') 
                            ->send();
                    } ),

                Action::make('edit')
                    ->label('Settings')           
                    ->url(fn (): string => route('filament.dashboard.resources.sites.backups_settings', ['record' => $this->site_id ? $this->site_id : '2', 'tenant' => $this->getTenant()->slug ], false)),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete())
            ]);
    }

    public function getTenant()
    {
        return Filament::getTenant(); 
    }

    // public function getTabs(): array
    // {
    //     return [
    //         'all' => Tab::make(),
    //         'active' => Tab::make()
    //             ->modifyQueryUsing(fn (Builder $query) => $query->where('enabled', true)),
    //         'inactive' => Tab::make()
    //             ->modifyQueryUsing(fn (Builder $query) => $query->where('enabled', false)),
    //     ];
    // }
    
    public function render(): View
    {
        return view('livewire.list-backups'); 
    }
}