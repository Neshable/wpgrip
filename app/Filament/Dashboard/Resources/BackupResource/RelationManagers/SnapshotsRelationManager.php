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
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('size'),
                Tables\Columns\TextColumn::make('deletion_date')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('restore')
                        ->requiresConfirmation()
                        ->label('Restore Backup')
                        ->modalHeading('Process of restoring a backup')
                        ->modalSubmitActionLabel('Restore')
                        ->successNotificationTitle('Are you sure you want to restore this backup?')
                        ->color('info')
                        ->icon('heroicon-o-server-stack')
                        ->modalIcon('heroicon-o-server-stack')
                        ->action(fn (Snapshot $record) => $record->delete()),
                    Tables\Actions\Action::make('download')
                        ->color('info')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action( function(Snapshot $record) {
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
