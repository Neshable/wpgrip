<?php

namespace App\Filament\Dashboard\Resources;

use App\Filament\Dashboard\Resources\ServerResource\Pages;
use App\Filament\Dashboard\Resources\ServerResource\RelationManagers;
use App\Models\Server;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
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

use Filament\Schemas\Components\Section;
use Filament\Infolists;
use Illuminate\Support\HtmlString;

use Filament\Actions\Action;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;

    protected static ?int $navigationSort = 2;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-server-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
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
                    ->sortable()
                    ->html()
                    ->formatStateUsing(function ($state, Server $record) {
                        $iconName = $record->provider?->getIcon() ?? 'ubuntu';
                        $extension = 'svg';

                        if ($record->provider === HostingProvider::SiteGround) {
                            $iconName = 'siteground';
                            $extension = 'png';
                        }

                        return new HtmlString(
                            '<div class="flex mr-4 gap-2 items-center">'.
                            ' <img src="'.asset('images/hosting-providers/'.$iconName.'.'.$extension).'" class="h-6 w-6" style="min-width: 1.5rem;" alt="'.$record->provider?->getLabel().'" title="'.$record->provider?->getLabel().'"> '
                            .$state
                            .'</div>'
                        );
                    })
                    ->tooltip(fn (Server $record): ?string => $record->provider?->getLabel()),
                // Tables\Columns\TextColumn::make('provider')
                //     ->badge(),
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
                    Actions\ActionGroup::make([
                        Actions\EditAction::make(),
                        Actions\ViewAction::make(),
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
                        Actions\DeleteAction::make()
                    ])->icon('heroicon-m-ellipsis-vertical')->link()
                    ->label(''),        
                ]
            )
            // ->contentGrid([
            //     'md' => 2,
            //     'xl' => 3,
            // ])
            ->bulkActions([
                // Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        
        return $schema
            ->schema([
                Section::make('Server Overview')
                    ->icon('heroicon-o-server')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('name')
                                        ->label('Friendly Name')
                                        ->weight(\Filament\Support\Enums\FontWeight::Bold)
                                        ->icon('heroicon-m-server'),
                                    Infolists\Components\TextEntry::make('provider')
                                        ->badge(),
                                ]),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('ip')
                                        ->label('Public IP')
                                        ->icon('heroicon-m-globe-alt')
                                        ->copyable()
                                        ->copyMessage('IP copied to clipboard'),
                                    Infolists\Components\TextEntry::make('private_ip')
                                        ->label('Private IP')
                                        ->icon('heroicon-m-lock-closed')
                                        ->placeholder('N/A')
                                        ->copyable()
                                        ->copyMessage('Private IP copied to clipboard'),
                                ]),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('ssh_port')
                                        ->label('SSH Port')
                                        ->icon('heroicon-m-command-line')
                                        ->fontFamily(\Filament\Support\Enums\FontFamily::Mono),
                                ]),
                            ]),
                    ]),
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
