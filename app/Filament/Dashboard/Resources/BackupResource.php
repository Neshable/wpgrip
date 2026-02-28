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
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;

class BackupResource extends Resource
{
    protected static ?string $model = Backup::class;

    protected static ?string $navigationIcon = 'icon-backups';

    protected static ?string $navigationLabel = 'Backups';

    // Visible in nav, sorted above Clients (sort 4) and Repositories (sort 3)
    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 3;

    // Scope to the current tenant
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', 'db') // DB-only
            ->where('tenant_id', filament()->getTenant()?->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Database Backup')
                    ->schema([
                        Forms\Components\Select::make('site_id')
                            ->label('Website')
                            ->options(
                                Site::where('tenant_id', filament()->getTenant()?->id)
                                    ->where('is_staging', false)
                                    ->pluck('name', 'id')
                            )
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('retention_days')
                            ->label('Keep snapshots for')
                            ->options([
                                '7'  => '1 week',
                                '14' => '2 weeks',
                                '30' => '1 month',
                                '60' => '2 months',
                                '90' => '3 months',
                            ])
                            ->default('30')
                            ->required(),

                        Forms\Components\TextInput::make('excluded_tables')
                            ->label('Excluded tables (comma-separated)')
                            ->placeholder('e.g. wp_options, wp_postmeta')
                            ->maxLength(255)
                            ->nullable(),
                    ])->columns(1),

                Section::make('Storage')
                    ->schema([
                        Forms\Components\Select::make('provider')
                            ->options(['s3' => 'Amazon S3'])
                            ->default('s3')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site.name')
                    ->label('Site')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('snapshots_count')
                    ->label('Snapshots')
                    ->counts('snapshots')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('size')
                    ->label('Total size')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '—';
                        $mb = $state / 1048576;
                        return $mb < 1024
                            ? number_format($mb, 1) . ' MB'
                            : number_format($mb / 1024, 2) . ' GB';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_backup')
                    ->label('Last backup')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('retention_days')
                    ->label('Retention')
                    ->formatStateUsing(fn ($state) => $state ? $state . ' days' : '—'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'failed' => 'danger',
                        default  => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
                Infolists\Components\Section::make('Backup configuration')
                    ->schema([
                        Infolists\Components\TextEntry::make('site.name')->label('Website'),
                        Infolists\Components\TextEntry::make('provider')->label('Storage'),
                        Infolists\Components\TextEntry::make('retention_days')
                            ->label('Retention')
                            ->formatStateUsing(fn ($state) => $state . ' days'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'active' => 'success',
                                'failed' => 'danger',
                                default  => 'gray',
                            }),
                    ])->columns(4),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBackups::route('/'),
            'create' => Pages\CreateBackup::route('/create'),
            'view'   => Pages\ViewBackup::route('/{record}'),
            'edit'   => Pages\EditBackup::route('/{record}/edit'),
        ];
    }
}
