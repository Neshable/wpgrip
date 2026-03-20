<?php

namespace App\Livewire;
 
use App\Models\Site;
use App\Models\Backup;
use App\Models\Snapshot;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

use App\Filament\Dashboard\Resources\BackupResource\Pages;

use App\Jobs\Backup\Database\ManualRemoteDBBackup;
use App\Jobs\Backup\RemoteFilesBackup;
use Filament\Forms\Components\Select;

use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Actions;

use Filament\Tables\Columns\ViewColumn;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use Filament\Actions\BulkAction;
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
    use InteractsWithActions;

    public $site_id;
    
    public function table(Table $table): Table
    {   
        // $backupIds = Backup::query()->where('site_id', $this->site_id)->pluck('id');
        // Snapshot::query()->whereIn('backup_id', $backupIds );

        return $table
            ->query( Backup::query()->where('site_id', $this->site_id) )
            ->deferLoading()
            ->columns([
                // Tables\Columns\ViewColumn::make('site')
                // ->view('filament.tables.columns.sitename'),
                // TextColumn::make('site.name'),
                TextColumn::make('type')
                    ->label('Backup Type')
                    ->icon(fn (string $state): string => match ($state) {
                        'db' => 'heroicon-m-circle-stack',
                        'files' => 'heroicon-m-folder',
                        default => 'heroicon-m-circle-stack',
                    })->tooltip(fn (string $state): string => $state),        

                // IconColumn::make('provider')
                // ->icon(fn (string $state): string => match ($state) {
                //     's3' => 'icon-s3',
                //     'reviewing' => 'heroicon-o-clock',
                //     'published' => 'heroicon-o-check-circle',
                // })->tooltip(fn (string $state): string => $state),
 
                // TextColumn::make('size')
                //     ->numeric()
                //     ->icon('heroicon-m-ellipsis-horizontal-circle')
                //     ->color('primary')
                //     ->sortable(),
                TextColumn::make('frequency')
                    ->formatStateUsing(fn (string $state, $record): string => match ($state) {
                        '1' => 'Daily',
                        '3' => 'Bi-Weekly',
                        '7' => 'Weekly',
                        '14' => 'Every 2nd Week',
                        '30' => 'Monthly',
                        default => $state,
                    })
                    ->sortable(),
                // TextColumn::make('retention_days')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('last_backup')
                    ->date()
                    ->sortable(),
                TextColumn::make('next_backup')
                    ->date(),
                TextColumn::make('snapshots_count')
                    ->label('Available Snapshots')
                    ->badge()
                    ->counts('snapshots'),
             
                // Tables\Columns\IconColumn::make('enabled')
                //     ->label('Status')
                //     ->boolean(),
                // TextColumn::make('created_at')->dateTime()->since(),
                // TextColumn::make('backup.frequency')
                //     ->label('Parent Backup Job')
                //     ->formatStateUsing(fn (string $state, $record): string => match ($state) {
                //         '1' => 'Daily',
                //         '3' => 'Bi-Weekly',
                //         '7' => 'Weekly',
                //         '14' => 'Every 2nd Week',
                //         '30' => 'Monthly',
                //         default => $state,
                //     }),
                // TextColumn::make('backup.type')
                //     ->label('Type')
                //     ->icon(fn (string $state): string => match ($state) {
                //         'db' => 'heroicon-m-circle-stack',
                //         'files' => 'heroicon-m-folder',
                //         default => 'heroicon-m-circle-stack',
                //     })->tooltip(fn (string $state): string => $state),
                // TextColumn::make('status')
                // ->badge()
                // ->color(fn (string $state): string => match ($state) {
                //     'pending' => 'gray',
                //     'archived' => 'warning',
                //     'cleaning' => 'warning',
                //     'completed' => 'success',
                //     default => 'gray'
                // }),
                // TextColumn::make('size')
                // ->formatStateUsing(function (string $state, $record) {
                //     return number_format($record->size / 1024 / 1024, 2);
                // })
                // ->suffix('MB'),
                // TextColumn::make('deletion_date')->dateTime(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('view')
                        ->label('View')
                        ->url(fn (Backup $record): string => Pages\ViewBackup::getUrl(['record' => $record])),
                        // ->openUrlInNewTab()
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->url(fn (Backup $record): string => Pages\ViewBackup::getUrl(['record' => $record])),
                        // ->openUrlInNewTab()
                    Action::make('pause')
                        // ->icon('heroicon-o-arrow-down-tray')
                        ->action( function(Backup $record ) {
                        }),
                    Action::make('edit')
                        // ->icon('heroicon-o-arrow-down-tray')
                        ->action( function(Backup $record ) {
                        }),
                    Action::make('restore')
                        // ->icon('heroicon-o-arrow-down-tray')
                        ->action( function(Backup $record ) {
                        }),
                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->headerActions([
                
                // Action::make('create')
                        // ->requiresConfirmation()
                        // ->label('Backup DB Manually')
                        // ->modalHeading('Creating a new manual backup')
                        // ->modalSubmitActionLabel('Create')
                        // ->color('warning')
                        // ->icon('heroicon-o-server-stack')
                        // ->modalIcon('heroicon-o-server-stack')
                        // ->action(function () {
                        //     // Set the site model.
                        //     $record = Site::find( $this->site_id );
                        //     ManualRemoteDBBackup::dispatchSync( $record, 'manual' );
                        //     // RemoteDBBackup::dispatch( $record, 'manual' )->onQueue('longrunning');
                        //     Notification::make()
                        //         ->title('Backup process started.')
                        //         ->success()
                        //         ->body('The process will finish in the background. A notification will appear once it\'s completed.') 
                        //         ->send();
                        // } ),

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