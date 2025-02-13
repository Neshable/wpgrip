<?php

namespace App\Filament\Dashboard\Resources;

use App\Filament\Dashboard\Resources\SiteResource\Pages;
use App\Filament\Dashboard\Resources\SiteResource\RelationManagers;

use Filament\Facades\Filament;

use App\Models\Site;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Enums\HostingProvider;
use App\Enums\ServerType;
use App\Enums\BoardType;

use Illuminate\Support\Facades\Auth;

use App\Jobs\Site\SyncSiteStats;
use Filament\Notifications\Notification;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Infolists\Components\IconEntry;
use Illuminate\Contracts\Support\Htmlable;

use Filament\Support\Enums\FontWeight;

use Filament\Notifications\Actions\Action;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $modelLabel = 'Site';

    public static function form(Form $form): Form
    {
        return $form     
        ->schema([        
            Forms\Components\Wizard::make([
               
                Forms\Components\Wizard\Step::make('Main Info')
                    // ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Forms\Components\Toggle::make('is_staging')
                            ->label('Will this be a staging site?')
                            ->helperText(new HtmlString('Enable the staging mode if this is a staging site.'))
                            ->inline()
                            ->live(),
                        Forms\Components\Select::make('parent_id')
                            ->label('Choose live website to connect with')
                            ->relationship(
                                'parent', 
                                'name',
                                // Exclude the stagings and scope to current tenant.
                                modifyQueryUsing: fn (Builder $query) => $query->where('is_staging', false)->whereBelongsTo(Filament::getTenant())
                            )
                            ->searchable(['name', 'url'])
                            ->preload()
                            // ->required(fn (Get $get): bool => filled($get('is_staging')))
                            ->visible(fn ($get): bool => !empty($get('is_staging'))),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->helperText(new HtmlString('Friendly name that helps you identify your site faster.'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('url')
                            ->url()
                            ->helperText(new HtmlString('The <strong>full url</strong> of the wesbite.'))        
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ssh_user')
                            ->helperText(new HtmlString('The <strong>ssh user</strong> who owns the directory and who can login.'))  
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('dir_path')
                            ->helperText(new HtmlString('The <strong>absolute</strong> dir path to your wordpress site.'))  
                            ->required()
                            ->maxLength(255),
                        // Forms\Components\Toggle::make('uptime_monitor')
                        // ->helperText(new HtmlString('Activate uptime monitor.'))
                        // ->visible(fn ($get): bool => empty($get('is_staging')))
                        // ->inline(),
                    ]),
                // Second step
                Forms\Components\Wizard\Step::make('Client and Server')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->required()
                            ->relationship(
                                name: 'client',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()))
                            ->helperText(new HtmlString('Choose existing client or add a new one.'))
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->email(),
                                Forms\Components\TextInput::make('country')
                                    ->label('Country')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes')
                                    ->autosize(),

                            ]),
                        Forms\Components\Select::make('server_id')
                            ->required()
                            ->relationship(
                                name: 'server',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()))
                            ->helperText(new HtmlString('Choose existing server/hosting or add a new one.'))
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('ip')
                                    ->required()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('ssh_port')
                                    ->label('SSH Port')
                                    ->maxLength(8)
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes')
                                    ->autosize(),
                                Forms\Components\Select::make('provider')
                                    ->options(HostingProvider::class)
                                    ->searchable(),
                                Forms\Components\Select::make('type')
                                    ->options(ServerType::class)
                                    ->searchable()
                            ]), 
                        Forms\Components\Select::make('board_provider')
                            ->label('Project management tool')
                            ->options(BoardType::class)
                            ->searchable(),
                        Forms\Components\TextInput::make('board_url')
                            ->label('Link to working board')
                            ->helperText(new HtmlString('The <strong>direct url</strong> to the project management tool.'))  
                            ->maxLength(320),
    
                    ]),
                // Third step
                // Forms\Components\Wizard\Step::make('Backup')
                //     ->schema([
                //         Forms\Components\Section::make('Backup Schedule')
                //             ->description('Specify the schedule you want a backup to occur')
                //             ->schema([
                //                 // Forms\Components\Toggle::make('backup_enabled')
                //                 // ->label('Enable backup'),
                //                 // Forms\Components\Select::make('db_schedule')
                //                 //     ->options([
                //                 //         '12hours' => 'Twice a Day',
                //                 //         'daily' => 'Daily',
                //                 //         'biweekly' => 'BiWeekly',
                //                 //         'weekly' => 'Weekly',
                //                 //         'monthly' => 'Monthly',
                //                 //     ]),
                //                 // Forms\Components\Select::make('files_schedule')
                //                 //     ->options([
                //                 //         'daily' => 'Daily',
                //                 //         'biweekly' => 'BiWeekly',
                //                 //         'weekly' => 'Weekly',
                //                 //         'monthly' => 'Monthly',
                //                 //     ])
                //         ]),
                //         Forms\Components\Section::make('Backup Exclusion')
                //             ->description('Specify which files/folders to exclude from the backup. Put each on new line.')
                //             ->schema([
                //                 Forms\Components\Textarea::make('Excluded paths')
                //                     ->rows(10)
                //                     ->cols(20)
                //             ])
                //     ]),
            ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Add new site
                </x-filament::button>
            BLADE)))
            ->columnSpan('full')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()))
        ->groups([
            'client.name',
            'server.name',
        ])
        ->defaultPaginationPageOption(25)
        // ->defaultGroup('client.name')
        ->deferLoading()
        // See https://filamentphp.com/docs/3.x/tables/advanced#custom-row-classes
        ->recordClasses(function (Site $record) {
          if ( !$record->ssh_connection ) {
            return 'border-s-2 border-red-600 bg-red-50 dark:bg-red-950 dark:border-red-300';
          }
          return null;
        })
        ->columns( self::inputTable() )
        ->filters([
            Tables\Filters\SelectFilter::make('Filter by Client')
                ->searchable()
                ->label('Filter by Client')
                ->preload()
                ->indicator('Client')
                ->relationship('client', 'name'),
            Tables\Filters\SelectFilter::make('Filter by Server')
                ->multiple()
                ->label('Filter by Server')
                ->preload()
                ->indicator('Server')
                ->relationship('server', 'name'),
            // TernaryFilter::make('is_admin'),
            Tables\Filters\Filter::make('is_featured')
            ->toggle()
            //->query(fn (Builder $query): Builder => $query->where('is_featured', true))
        ])
        ->filtersTriggerAction(
            fn ($action) => $action
                ->button()
                ->label('Filter'),
        )
        ->actions(
            [
         
            Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                
                Tables\Actions\Action::make('trello')
                    ->label('View trello')
                    ->icon('icon-trello')
                    ->tooltip('Open the trello board')
                    ->url(fn (Site $record): ?string => $record->board_url)
                    ->openUrlInNewTab(),
               
                Tables\Actions\Action::make('sync')
                    ->action(function ( Site $record) {
                        SyncSiteStats::dispatchSync($record);
                        Notification::make()
                            ->title('Syncing.')
                            ->success()
                            ->body('Checking connection.') 
                            ->send();
              
                    } )
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Sync website?')
                    ->modalDescription('Are you sure you\'d like to sync this website?')
                    ->modalSubmitActionLabel('Yes, sync now')
                    ->tooltip('Sync this site'),

                Tables\Actions\DeleteAction::make()
                ->modalDescription('Are you sure you want to delete this website? The backups will be also deleted.')
            ]),
           
            ])
        // ->heading('All available websites in this team')
        // ->description('Manage all ')
        ->headerActions([
            //Tables\Actions\Action::make('create')
                
            ])
        ->emptyStateHeading('No sites found')
        ->emptyStateDescription('You haven\'t added any website yet.')
        ->bulkActions([
            // Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

     /**
     * Extract table rows here so we can reuse them across the app.
     *
     * @return array
     */
    public static function inputTable(): array {
        return [
            // Grid::make(),
            // Tables\Columns\ImageColumn::make('screenshot_path')->disk('local')
            //     ->label('Screenshot')
            //     ->extraImgAttributes(['loading' => 'lazy'])
            //     ->width(250)
            //     ->height(150),
          
            // Tables\Columns\IconColumn::make('ssh_connection')
            //     ->label(false)
            //     ->boolean()
            //     ->size(Tables\Columns\IconColumn\IconColumnSize::Small)
            //     ->tooltip(fn (Site $record): string => $record->ssh_connection ? 'Connected' : 'Issue with SSH connection' )
            //     ->trueIcon('heroicon-o-check-circle')
            //     ->falseIcon('heroicon-o-exclamation-circle'),
            Tables\Columns\ImageColumn::make('')
                ->circular()
                ->size(20)
                ->defaultImageUrl(fn (Site $record): string => url('https://s2.googleusercontent.com/s2/favicons?domain=' . $record->url)),
            
                Tables\Columns\ViewColumn::make('name')
                ->searchable()
                ->view('filament.tables.columns.sitename'),
                Tables\Columns\ViewColumn::make('status')
                ->label(false)
                ->view('filament.tables.columns.siteinfo'),
            Tables\Columns\TextColumn::make('server.name')
                ->label('Server/Hosting')
                ->searchable()
                ->description(fn (Site $record): string => $record->server->ip )
                ->copyable()
                ->sortable(),
            Tables\Columns\ViewColumn::make('stack')
                ->label('Stack')
                ->view('filament.tables.columns.stack'),
            Tables\Columns\ViewColumn::make('performance')->view('filament.tables.columns.sitespeed'),
            
   

            // Tables\Columns\TextColumn::make('server.provider')
            //     ->label('Hosted')
            //     ->badge(),
        
            // Tables\Columns\TextColumn::make('wp_ver')
            //     ->icon('icon-wordpress')
            //     ->weight(FontWeight::Bold)
            //     ->label('WP'),
            // Tables\Columns\TextColumn::make('php_ver')
            //     ->weight(FontWeight::Bold)
            //     ->label('PHP'),
            
            // Tables\Columns\ViewColumn::make('stack')->view('filament.tables.columns.stackinfo'), 
            // Tables\Columns\ViewColumn::make('PHP')
            //     ->label('PHP')
            //     ->view('filament.tables.columns.phpversion'),  
            // Tables\Columns\TextColumn::make('client.name')->label('Owner')->sortable(),
            // Tables\Columns\IconColumn::make('status')
            //     ->icon(fn (string $state): string => match ($state) {
            //         'draft' => 'heroicon-o-pencil',
            //         'reviewing' => 'heroicon-o-clock',
            //         'published' => 'heroicon-o-check-circle',
            //     }),
            // Tables\Columns\TextColumn::make('url')->label('URL'),
            // Tables\Columns\ToggleColumn::make('uptime_monitor'),
            // Tables\Columns\ToggleColumn::make('active_webhook_slack')->label('Slack Notifications'),
            // Tables\Columns\TextColumn::make('backups_count')
            //     ->counts('backups')
            //     ->label('Backups'),
           
           
            // Tables\Columns\TextColumn::make('php_ver')->label('PHP'),
          

            Tables\Columns\TextColumn::make('dir_size')
                ->label('Files')
                ->getStateUsing(function (Site $record) {
                    return $record->getFormatedDBSize();
                })
                ->sortable(),
                
            Tables\Columns\TextColumn::make('db_size')
                ->label('DB Size')
                ->getStateUsing(function (Site $record) {
                return $record->getDBSize();
                }),

            Tables\Columns\TextColumn::make('last_sync')
                ->label('Last sync')
                ->since()

            // IconColumn::make('is_ssl_active')
            // ->boolean()
            // ->trueIcon('')
            // ->falseIcon('heroicon-o-x-circle'),
            // ViewColumn::make('')->view('filament.tables.columns.allinfo'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected function beforeCreate(): void
    {
        $user = Auth::user();
        $this->halt();
        $tenant = Filament::getTenant(); 
        
        $siteCount = Site::where('tenant_id', $tenant->id)->count();
            if ($siteCount >= 1) {
                Notification::make()
                ->warning()
                ->title('You don\'t have an active subscription!')
                ->body('Choose a plan to continue.')
                ->persistent()
                ->actions([
                    Action::make('subscribe')
                        ->button()
                        ->url(route('subscribe'), shouldOpenInNewTab: true),
                ])
                ->send();
        
                $this->halt();
            }
        

        // if (! auth()->user()->team->subscribed()) {
           
        // }

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSites::route('/'),
            'create' => Pages\CreateSite::route('/create'),
            'edit' => Pages\EditSite::route('/{record}/edit'),
            'view' => Pages\ViewSite::route('/{record}'),

          
            'monitoring' => Pages\Monitoring::route('/{record}/monitoring'), 
            'monitors' => Pages\Monitors::route('/{record}/monitoring/monitors'),

            'blacklists' => Pages\Blacklists::route('/{record}/blacklists'), 
            

            'performance' => Pages\Performance::route('/{record}/performance'), 
            'performance_history' => Pages\PerformanceHistory::route('/{record}/performance/history'), 

            'access' => Pages\Access::route('/{record}/access'), 
            'database' => Pages\Database::route('/{record}/database'), 

            
            // 'backups_settings' => Pages\ShowBackupsSettings::route('/{record}/backups/settings'), 
            // Insights AI
            'insights' => Pages\Insights::route('/{record}/insights'), 
            
            // Updates tab
            'plugins' => Pages\Plugins::route('/{record}/plugins'), 
            'themes' => Pages\Themes::route('/{record}/themes'), 
            'core' => Pages\Core::route('/{record}/core'), 
            
            // Tests
            'tests' => Pages\Tests::route('/{record}/tests'), 
            // Eror monitoring
            'errors' => Pages\Errors::route('/{record}/errors'),
            
            'performance' => Pages\Performance::route('/{record}/tests/performance'), 
            
            'tools' => Pages\Tools::route('/{record}/tools'), 
            'security' => Pages\Security::route('/{record}/security'), 
            // 'checksums' => Pages\SecurityGroup\Checksumss::route('/{record}/security/checksums'),
            'repos' => Pages\Git::route('/{record}/repos'),
            'staging' => Pages\Staging::route('/{record}/staging'), 
            'backups' => Pages\ShowBackups::route('/{record}/backups'), 
            

            // Git repos subpages
            // 'repos.view' => Pages\Repos\ViewRepo::route('/{parent}/repository/{record}'),
            // 'repos.view' => ViewRepository::route('/{parent}/repos/{record}'),
        ];
    }
}
