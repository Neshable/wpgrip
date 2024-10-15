<?php

namespace App\Filament\Dashboard\Resources;

use App\Filament\Dashboard\Resources\BackupResource\Pages;
use App\Filament\Dashboard\Resources\BackupResource\RelationManagers;
use App\Models\Backup;
use App\Models\Site;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Infolists;
use Filament\Infolists\Infolist;

use Filament\Forms\Components\Section;

class BackupResource extends Resource
{
    protected static ?string $model = Backup::class;

    protected static ?string $navigationIcon = 'icon-backups';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Backup Instance')
                ->schema([
                    Forms\Components\Toggle::make('enabled')
                        ->required(),
                    Forms\Components\Select::make('site_id')
                        ->label('Website')
                        ->options(Site::all()->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    Forms\Components\Select::make('type')
                        ->options([
                            'db' => 'Database',
                            'files' => 'Files',
                        ]),
                    Forms\Components\Radio::make('frequency')
                        ->label('Backup Frequency')
                        ->options([
                            '1' => 'Daily',
                            '3' => 'Bi-Weekly',
                            '7' => 'Weekly',
                            '30' => 'Monthly',
                        ])
                        ->required()
                        ->inline()
                        ->inlineLabel(false),
                    Forms\Components\Radio::make('retention_days')
                        ->label('Retention period')
                        ->options([
                            '3' => '3 Days',
                            '7' => '1 Week',
                            '14' => '2 Weeks',
                            '30' => '1 Month',
                            '60' => '2 Months',
                            '90' => '3 Months',
                        ])
                        ->required()
                        ->inline()
                        ->inlineLabel(false)
                    // Forms\Components\TextInput::make('retention_days')
                    // ->numeric()
                    // ->default(null),
                ]),
                Section::make('Storage')
                ->schema([
                    Forms\Components\Select::make('provider')
                        ->required()
                        ->options([
                            's3' => 'Amazon S3',
                        ]),
                    Forms\Components\TextInput::make('excluded_tables')
                        ->maxLength(255)
                        ->default(null),
                    Forms\Components\TextInput::make('excluded_files')
                        ->maxLength(255)
                        ->default(null),
                    Forms\Components\Checkbox::make('is_admin')
                        ->label('Exclude .git, node_modules, vendors, and storage/logs directories'),
                    Forms\Components\Checkbox::make('is_admin2')
                        ->label('Exclude related backup & cache files/directories'),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\ViewColumn::make('site')
                // ->view('filament.tables.columns.sitename'),
                Tables\Columns\TextColumn::make('site.name'),
                Tables\Columns\TextColumn::make('type')
                    ->icon(fn (string $state): string => match ($state) {
                        'db' => 'heroicon-m-circle-stack',
                        'files' => 'heroicon-m-folder',
                        default => 'heroicon-m-circle-stack',
                    })->tooltip(fn (string $state): string => $state),        

                // Tables\Columns\IconColumn::make('provider')
                // ->icon(fn (string $state): string => match ($state) {
                //     's3' => 'icon-s3',
                //     'reviewing' => 'heroicon-o-clock',
                //     'published' => 'heroicon-o-check-circle',
                // })->tooltip(fn (string $state): string => $state),
 
                // Tables\Columns\TextColumn::make('size')
                //     ->numeric()
                //     ->icon('heroicon-m-ellipsis-horizontal-circle')
                //     ->color('primary')
                //     ->sortable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->formatStateUsing(fn (string $state, $record): string => match ($state) {
                        '1' => 'Daily',
                        '3' => 'Bi-Weekly',
                        '7' => 'Weekly',
                        '14' => 'Every 2nd Week',
                        '30' => 'Monthly',
                        default => $state,
                    })
                    ->sortable(),
                // Tables\Columns\TextColumn::make('retention_days')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('last_backup')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_backup')
                    ->date(),
                Tables\Columns\IconColumn::make('enabled')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SnapshotsRelationManager::class,
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
          
                Infolists\Components\Section::make('Backup')
                    ->description('Backup job details')
                    ->schema([
                        Infolists\Components\TextEntry::make('site.name')
                        ->label('Website'),
                        Infolists\Components\TextEntry::make('type')
                        ->icon('heroicon-m-circle-stack')
                        ->label('Type of Backup'),
                        Infolists\Components\TextEntry::make('frequency')
                        ->icon('heroicon-m-calendar-days')
                        ->formatStateUsing(fn (string $state, $record): string => match ($state) {
                            '1' => 'Daily',
                            '3' => 'Bi-Weekly',
                            '7' => 'Weekly',
                            '14' => 'Every 2nd Week',
                            '30' => 'Monthly',
                            default => $state,
                        })
                        ->tooltip('How often the backup is triggered')
                        ->label('Frequency'),
                        Infolists\Components\TextEntry::make('retention_days')
                        ->tooltip('After how many days the backup is deleted')
                        ->label('Retention Days'),
  
                    ])->columns(4),
   
            ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBackups::route('/'),
            'create' => Pages\CreateBackup::route('/create'),
            'view' => Pages\ViewBackup::route('/{record}'),
            'edit' => Pages\EditBackup::route('/{record}/edit'),
        ];
    }
}
