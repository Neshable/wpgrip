<?php

namespace App\Filament\Dashboard\Resources\BackupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

use App\Models\Snapshot;
use App\Jobs\Backup\ChainFilesBackup;
use App\Jobs\Backup\ChainDbBackup;


class SnapshotsRelationManager extends RelationManager
{
    protected static string $relationship = 'snapshots';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('created_at')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('backup_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('backup_id')
            ->poll('15s')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'gray',
                    'archived' => 'warning',
                    'cleaning' => 'warning',
                    'completed' => 'success',
                    default => 'gray'
                }),
                Tables\Columns\TextColumn::make('size')
                ->formatStateUsing(function (string $state, $record) {
                    return number_format($record->size / 1024 / 1024, 2);
                })
                ->suffix('MB'),
                Tables\Columns\TextColumn::make('deletion_date')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->description('List of all snapshots. The manual backups are currently limited to 3.')
            ->headerActions([
                Tables\Actions\Action::make('triggerBackup')
                    ->link()
                    ->requiresConfirmation()
                    // ->visible(function() {
                    //      // Count the number of Snapshot models with 'type' as 'manual' and 'backup_id' as the id of the owner record
                    //     $count = Snapshot::where('type', 'manual')
                    //         ->where('backup_id', $this->getOwnerRecord()->id)
                    //         ->count();

                    //     // Return true if the count is less than 3, and false otherwise
                    //     return $count < 3;
                    // })
                    ->label('Backup now')
                    ->modalHeading('Backup Now')
                    ->modalSubmitActionLabel('Backup now')
                    ->modalSubmitAction(function() {
                        // Count the number of Snapshot models with 'type' as 'manual' and 'backup_id' as the id of the owner record
                        $count = Snapshot::where('type', 'manual')
                            ->where('backup_id', $this->getOwnerRecord()->id)
                            ->count();

                        // Return true if the count is less than 3, and false otherwise
                        if ( $count >= 3 ){
                            return false;
                        }
                    })
                    ->modalDescription('Click Backup Now to create new ondemand backup. You can create up to 3 manual backups.')
                    ->successNotificationTitle('Click Backup Now to create new ondemand backup.')
                    ->color('info')
                    ->icon('icon-backups')
                    ->modalIcon(false)
                    ->action(function() {
                        switch( $this->getOwnerRecord()->type ) {
                            case 'files':
                                ChainFilesBackup::dispatchSync( $this->getOwnerRecord(), 'manual' );
                                break;
                            case 'db':
                                ChainDbBackup::dispatchSync( $this->getOwnerRecord(), 'manual' );
                                break;
                            // default:
                            //     ChainDbBackup::dispatchSync( $this->getOwnerRecord() );
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\Action::make('restore')
                    //     ->requiresConfirmation()
                    //     ->label('Restore Backup')
                    //     ->modalHeading('Process of restoring a backup')
                    //     ->modalSubmitActionLabel('Restore')
                    //     ->successNotificationTitle('Are you sure you want to restore this backup?')
                    //     ->icon('heroicon-o-server-stack')
                    //     ->modalIcon('heroicon-o-server-stack')
                    //     ->action(fn (Snapshot $record) => $record->delete()),
                    Tables\Actions\Action::make('download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action( function($record) {
                            if ( $record->getS3path() ) 
                            {
                                if( Storage::disk('s3')->exists( $record->getS3path() ) )
                                { 
                                    return Storage::disk('s3')->download( $record->getS3path() );  

                                    // $asset = Asset::find($id);
                                    // $assetPath = Storage::disk('s3')->url($record->file_path);
                            
                                    // header("Cache-Control: public");
                                    // header("Content-Description: File Transfer");
                                    // header("Content-Disposition: attachment; filename=" . basename($assetPath));
                                    // header("Content-Type: " . $asset->mime);
                            
                                    // return readfile($assetPath);
                                }
                            }
                            
                            return false;
                    
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
