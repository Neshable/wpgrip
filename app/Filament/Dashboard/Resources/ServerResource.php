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

use Filament\Tables\Columns\ImageColumn;

use Filament\Notifications\Notification;
use Filament\Facades\Filament;

use App\Enums\HostingProvider;
use App\Enums\ServerType;
use App\Filament\Dashboard\Resources\SiteResource\RelationManagers\SitesRelationManager;
use App\Jobs\GetServerStats;

use Filament\Infolists\Components\Section;
use Filament\Infolists;
use Filament\Infolists\Infolist;

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
                    ->maxLength(255)
                    ->helperText('A friendly name to identify your server easily.'),
                Forms\Components\TextInput::make('ip')
                    ->required()
                    ->unique(Server::class, 'ip', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'A server with this IP address already exists.',
                    ])
                    ->helperText('The public IP address of your server.'),
                Forms\Components\TextInput::make('private_ip')
                    ->label('Private IP')
                    ->helperText('The private IP address of your server (optional).'),
                Forms\Components\TextInput::make('ssh_port')
                    ->required()
                    ->maxLength(255)
                    ->helperText('The SSH port used to connect to your server (default is 22).'),
                Forms\Components\Select::make('provider')
                    ->options(HostingProvider::class)
                    ->searchable()
                    ->helperText('Select the hosting provider for this server.'),
                Forms\Components\Select::make('type')
                    ->options(ServerType::class)
                    ->searchable()
                    ->helperText('Choose the server type (e.g., Web Server, Database Server).'),

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
                    ->searchable()
                    ->sortable(), 
                Tables\Columns\TextColumn::make('provider')
                    ->badge(),
                Tables\Columns\TextColumn::make('ip')
                    ->copyable()
                    ->copyMessage('IP copied to clipboard')
                    ->searchable()
                    ->label('Public IP')
                    ->icon('heroicon-m-clipboard-document'),
                Tables\Columns\TextColumn::make('private_ip')
                    ->copyable()
                    ->copyMessage('Private IP copied to clipboard')
                    ->copyMessageDuration(1500)
                    ->label('Private IP')
                    ->icon('heroicon-m-clipboard-document'),

                Tables\Columns\TextColumn::make('ssh_port')
                    ->label('SSH Port'),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        
        return $infolist
            ->schema([
                Section::make('Server Overview')
                ->description('')
                ->schema([
                    Infolists\Components\TextEntry::make('name')->label('Friendly Name'),
                    Infolists\Components\TextEntry::make('provider')->badge(),
                    Infolists\Components\TextEntry::make('ip')
                        ->copyable()
                        ->copyMessage('IP copied to clipboard')
                        ->label('Public IP')
                        ->icon('heroicon-m-clipboard-document'),
                    Infolists\Components\TextEntry::make('private_ip')
                        ->copyable()
                        ->copyMessage('Private IP copied to clipboard')
                        ->copyMessageDuration(1500)
                        ->label('Private IP')
                        ->icon('heroicon-m-clipboard-document'),
                    Infolists\Components\TextEntry::make('ssh_port')->label('SSH Port'),

                ])->columns(2)
            ]);

    
           
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SitesRelationManager::class,
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
            'view' => Pages\ViewServer::route('/{record}'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }
}
