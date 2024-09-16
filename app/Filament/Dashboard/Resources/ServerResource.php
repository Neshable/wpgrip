<?php

namespace App\Filament\Dashboard\Resources;

use App\Filament\Dashboard\Resources\ServerResource\Pages;
use App\Filament\Dashboard\Resources\ServerResource\RelationManagers;
use App\Models\Server;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Columns\IconColumn;

use Filament\Notifications\Notification;
use Filament\Facades\Filament;

use App\Enums\HostingProvider;
use App\Enums\ServerType;
use App\Filament\Dashboard\Resources\SiteResource\RelationManagers\SitesRelationManager;
use App\Jobs\GetServerStats;

use Filament\Tables\Actions\Action;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ip')
                    ->required(),
                Forms\Components\TextInput::make('private_ip')
                    ->label('Private IP'),
                Forms\Components\TextInput::make('ssh_port')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('provider')
                    ->options(HostingProvider::class)
                    ->searchable(),
                Forms\Components\Select::make('type')
                    ->options(ServerType::class)
                    ->searchable()

                // Field::make('ip_address')->ip()
                // Field::make('ip_address')->ipv4()
                // Field::make('ip_address')->ipv6()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->groups([
            //     'provider',
            //     'type',
            // ])
            // ->defaultGroup('type')
            ->deferLoading()
            ->columns([
                // Tables\Columns\Layout\Stack::make([
                //     Tables\Columns\TextColumn::make('provider')
                //     ->label('Provider')
                //     ->badge(),
                //     Tables\Columns\ViewColumn::make('info')->view('filament.tables.columns.serverinfo'),              
                // ]),
                 Tables\Columns\TextColumn::make('name')
                    ->label('Friendly Name')
                    ->sortable(),
                 Tables\Columns\TextColumn::make('provider')
                    ->badge(),
                Tables\Columns\TextColumn::make('ip')
                    ->copyable()
                    ->copyMessage('IP copied to clipboard')
                    ->label('Public IP')
                    ->icon('heroicon-m-clipboard-document'),
                Tables\Columns\TextColumn::make('private_ip')
                    ->copyable()
                    ->copyMessage('Private IP copied to clipboard')
                    ->copyMessageDuration(1500)
                    ->label('Private IP')
                    ->icon('heroicon-m-clipboard-document'),

                Tables\Columns\TextColumn::make('ssh_port'),
                Tables\Columns\TextColumn::make('sites_count')
                    ->badge()
                    ->label('Sites')
                    ->counts('sites')
                    ->color('success'),
                   
                    // Tables\Columns\TextColumn::make('cpu_cores'),
                    // Tables\Columns\TextColumn::make('ip'),
                    // Tables\Columns\TextColumn::make('hdd_total'),
                    // Tables\Columns\ViewColumn::make('info')->view('filament.tables.columns.serverinfo'),   
                    // Tables\Columns\TextColumn::make('ram_total'),

                
            ])
            ->filters([
                //
            ])
            ->actions(
                [
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\ViewAction::make(),
                        Action::make('sync')
                            ->action(function ( Server $record) {
                                // $meta = $record->sitemeta;
                                // dd($meta->active_theme);
                                GetServerStats::dispatch($record);
                                
                            } )
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                            ->requiresConfirmation()
                            ->modalHeading('Sync Server?')
                            ->modalSubheading('Are you sure you\'d like to sync this server?')
                            ->modalButton('Yes, sync now')
                            ->tooltip('Sync this server'),
                        Tables\Actions\DeleteAction::make()
                    ])->icon('heroicon-m-ellipsis-vertical')->link()
                    ->label(''),        
                ]
            )
            // ->contentGrid([
            //     'md' => 2,
            //     'xl' => 3,
            // ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {   
        // By default this is relation BelongsToMany but we need to get only one team.
        return parent::getEloquentQuery()->whereBelongsTo( Filament::getTenant() );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }
}
