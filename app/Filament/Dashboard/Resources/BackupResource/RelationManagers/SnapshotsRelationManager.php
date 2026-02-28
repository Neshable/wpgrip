<?php

namespace App\Filament\Dashboard\Resources\BackupResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

use App\Models\Snapshot;
use App\Jobs\Backup\ChainDbBackup;

class SnapshotsRelationManager extends RelationManager
{
    protected static string $relationship = 'snapshots';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->poll('10s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'gray',
                        'archived'  => 'info',
                        'cleaning'  => 'warning',
                        'completed' => 'success',
                        'failed'    => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '—';
                        $mb = $state / 1048576;
                        return $mb < 1024
                            ? number_format($mb, 1) . ' MB'
                            : number_format($mb / 1024, 2) . ' GB';
                    }),

                Tables\Columns\TextColumn::make('deletion_date')
                    ->label('Expires')
                    ->date(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('backupNow')
                    ->label('Back up now')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Create database backup')
                    ->modalDescription('A snapshot of the database will be created and uploaded to S3. This runs in the background and may take a few minutes.')
                    ->modalSubmitActionLabel('Start backup')
                    ->action(function () {
                        $backup = $this->getOwnerRecord();

                        // Dispatch the chain — runs async on the longrunning queue
                        ChainDbBackup::dispatch($backup, 'manual');

                        $this->notify('success', 'Backup queued. The snapshot list will update automatically.');
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->visible(fn (Snapshot $record) => $record->status === 'completed')
                        ->action(function (Snapshot $record) {
                            $s3Path = $record->getS3path();
                            if ($s3Path && Storage::disk('s3')->exists($s3Path)) {
                                return Storage::disk('s3')->download($s3Path);
                            }
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->before(function (Snapshot $record) {
                            // Also delete from S3 when deleting the record
                            $s3Path = $record->getS3path();
                            if ($s3Path && Storage::disk('s3')->exists($s3Path)) {
                                Storage::disk('s3')->delete($s3Path);
                            }
                        }),
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
