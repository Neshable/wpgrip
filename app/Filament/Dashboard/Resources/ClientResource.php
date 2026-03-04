<?php

namespace App\Filament\Dashboard\Resources;

use App\Enums\ClientSource;
use App\Enums\ClientStatus;
use App\Filament\Dashboard\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Clients';

    // -------------------------------------------------------------------------
    // Form
    // -------------------------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Client')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('company')
                                        ->maxLength(255),

                                    TextInput::make('email')
                                        ->required()
                                        ->email()
                                        ->maxLength(255),

                                    TextInput::make('phone')
                                        ->tel()
                                        ->maxLength(50),

                                    TextInput::make('website')
                                        ->url()
                                        ->maxLength(255)
                                        ->suffixIcon('heroicon-m-globe-alt'),

                                    Select::make('status')
                                        ->options(ClientStatus::class)
                                        ->default(ClientStatus::Active->value)
                                        ->required(),

                                    Select::make('source')
                                        ->options(ClientSource::class),
                                ]),
                            ]),

                        Tabs\Tab::make('Billing & Contract')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    TextInput::make('billing_email')
                                        ->email()
                                        ->maxLength(255),

                                    Select::make('currency')
                                        ->options([
                                            'USD' => 'USD – US Dollar',
                                            'EUR' => 'EUR – Euro',
                                            'GBP' => 'GBP – British Pound',
                                            'AUD' => 'AUD – Australian Dollar',
                                            'CAD' => 'CAD – Canadian Dollar',
                                            'CHF' => 'CHF – Swiss Franc',
                                            'BGN' => 'BGN – Bulgarian Lev',
                                        ])
                                        ->default('USD'),

                                    TextInput::make('monthly_value')
                                        ->numeric()
                                        ->prefix('$')
                                        ->maxValue(9999999.99),

                                    Forms\Components\Placeholder::make('')
                                        ->hiddenLabel(),

                                    DatePicker::make('contract_start')
                                        ->native(false)
                                        ->displayFormat('M d, Y'),

                                    DatePicker::make('contract_end')
                                        ->native(false)
                                        ->displayFormat('M d, Y')
                                        ->afterOrEqual('contract_start'),
                                ]),
                            ]),

                        Tabs\Tab::make('Address')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    TextInput::make('country')
                                        ->required()
                                        ->maxLength(100),

                                    TextInput::make('city')
                                        ->maxLength(100),

                                    TextInput::make('address')
                                        ->maxLength(255)
                                        ->columnSpanFull(),

                                    TextInput::make('postal_code')
                                        ->maxLength(20),

                                    Select::make('timezone')
                                        ->searchable()
                                        ->options(
                                            collect([
                                                'UTC',
                                                'America/New_York',
                                                'America/Chicago',
                                                'America/Denver',
                                                'America/Los_Angeles',
                                                'America/Anchorage',
                                                'America/Toronto',
                                                'America/Vancouver',
                                                'America/Sao_Paulo',
                                                'America/Mexico_City',
                                                'America/Argentina/Buenos_Aires',
                                                'Europe/London',
                                                'Europe/Berlin',
                                                'Europe/Paris',
                                                'Europe/Amsterdam',
                                                'Europe/Brussels',
                                                'Europe/Sofia',
                                                'Europe/Bucharest',
                                                'Europe/Rome',
                                                'Europe/Madrid',
                                                'Europe/Zurich',
                                                'Europe/Stockholm',
                                                'Europe/Helsinki',
                                                'Europe/Athens',
                                                'Europe/Istanbul',
                                                'Europe/Moscow',
                                                'Asia/Dubai',
                                                'Asia/Kolkata',
                                                'Asia/Singapore',
                                                'Asia/Shanghai',
                                                'Asia/Tokyo',
                                                'Asia/Seoul',
                                                'Asia/Hong_Kong',
                                                'Asia/Bangkok',
                                                'Australia/Sydney',
                                                'Australia/Melbourne',
                                                'Australia/Perth',
                                                'Pacific/Auckland',
                                                'Pacific/Honolulu',
                                                'Africa/Johannesburg',
                                                'Africa/Cairo',
                                                'Africa/Lagos',
                                            ])->mapWithKeys(fn (string $tz) => [$tz => str_replace(['/', '_'], [' / ', ' '], $tz)])->toArray()
                                        ),
                                ]),
                            ]),

                        Tabs\Tab::make('Notes & Tags')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                Textarea::make('notes')
                                    ->autosize()
                                    ->rows(4)
                                    ->columnSpanFull(),

                                TagsInput::make('tags')
                                    ->separator(',')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    // -------------------------------------------------------------------------
    // Table
    // -------------------------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant())
            )
            ->defaultSort('name')
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    TextColumn::make('name')
                        ->searchable()
                        ->sortable()
                        ->weight(FontWeight::Bold)
                        ->size(TextColumn\TextColumnSize::Large)
                        ->icon('heroicon-m-user')
                        ->description(fn (Client $record): ?string => $record->company),

                    TextColumn::make('email')
                        ->icon('heroicon-m-envelope')
                        ->color('gray')
                        ->copyable()
                        ->copyMessage('Email copied'),

                    TextColumn::make('phone')
                        ->icon('heroicon-m-phone')
                        ->color('gray')
                        ->placeholder('—'),

                    TextColumn::make('status')
                        ->badge(),

                    TextColumn::make('sites_count')
                        ->counts('sites')
                        ->badge()
                        ->color('primary')
                        ->formatStateUsing(fn ($state) => $state . ' ' . str('site')->plural((int) $state))
                        ->icon('heroicon-m-globe-alt'),

                    TextColumn::make('monthly_value')
                        ->money(fn (Client $record): string => $record->currency ?? 'USD')
                        ->sortable()
                        ->color('success')
                        ->icon('heroicon-m-banknotes')
                        ->placeholder('—'),

                    TextColumn::make('contract_end')
                        ->date('M d, Y')
                        ->since()
                        ->icon('heroicon-m-calendar')
                        ->color(fn (Client $record): string => match (true) {
                            $record->contract_end === null              => 'gray',
                            $record->contract_end->isPast()             => 'danger',
                            $record->contract_end->isBefore(now()->addDays(30)) => 'warning',
                            default                                     => 'gray',
                        })
                        ->placeholder('No contract end'),
                ])->space(3),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ClientStatus::class)
                    ->label('Status'),

                SelectFilter::make('source')
                    ->options(ClientSource::class)
                    ->label('Source'),

                TernaryFilter::make('has_active_contract')
                    ->label('Active Contract')
                    ->queries(
                        true: fn (Builder $query) => $query
                            ->whereNotNull('contract_end')
                            ->where('contract_end', '>=', now()),
                        false: fn (Builder $query) => $query
                            ->where(fn (Builder $q) => $q
                                ->whereNull('contract_end')
                                ->orWhere('contract_end', '<', now())
                            ),
                    ),
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
            ->emptyStateHeading('No clients yet')
            ->emptyStateDescription('Create your first client to get started.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add client')
                    ->icon('heroicon-m-plus'),
            ]);
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public static function getRelations(): array
    {
        return [];
    }

    // -------------------------------------------------------------------------
    // Pages
    // -------------------------------------------------------------------------

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view'   => Pages\ViewClient::route('/{record}'),
            'edit'   => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
