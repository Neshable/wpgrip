<?php

namespace App\Filament\Dashboard\Pages;

use App\Models\ActivityLog as ActivityLogModel;
use App\Models\Site;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class ActivityLog extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Activity Log';
    protected static ?string $title           = 'Activity Log';
    protected static ?string $slug            = 'activity-log';
    protected static ?int    $navigationSort  = 90;
    protected string $view            = 'filament.dashboard.pages.activity-log';

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function table(Table $table): Table
    {
        $tenantId = Filament::getTenant()->id;

        return $table
            ->query(
                ActivityLogModel::query()
                    ->where('tenant_id', $tenantId)
                    ->with(['user', 'site'])
                    ->latest('created_at')
            )
            ->columns([
                // Timestamp
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('M j, Y · H:i')
                    ->sortable()
                    ->size('sm')
                    ->color('gray'),

                // Who
                TextColumn::make('user.name')
                    ->label('User')
                    ->default('System')
                    ->size('sm')
                    ->weight('medium')
                    ->icon('heroicon-m-user-circle')
                    ->searchable(),

                // Category badge
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->size('sm')
                    ->color(fn (string $state): string => match ($state) {
                        'plugin'  => 'info',
                        'theme'   => 'warning',
                        'backup'  => 'success',
                        'git'     => 'gray',
                        'user'    => 'primary',
                        'site'    => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                // Action
                TextColumn::make('action')
                    ->label('Action')
                    ->size('sm')
                    ->formatStateUsing(fn (string $state) => ucwords(str_replace(['.', '_'], ' ', $state)))
                    ->searchable(),

                // Subject
                TextColumn::make('subject_label')
                    ->label('Target')
                    ->size('sm')
                    ->default('—')
                    ->searchable()
                    ->color('gray'),

                // Site
                TextColumn::make('site.name')
                    ->label('Site')
                    ->size('sm')
                    ->default('—')
                    ->color('gray'),

                // Status
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->size('sm')
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed'  => 'danger',
                        'pending' => 'warning',
                        default   => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'plugin' => 'Plugin',
                        'theme'  => 'Theme',
                        'backup' => 'Backup',
                        'git'    => 'Git',
                        'user'   => 'User',
                        'site'   => 'Site',
                        'system' => 'System',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed'  => 'Failed',
                        'pending' => 'Pending',
                    ]),

                SelectFilter::make('site_id')
                    ->label('Site')
                    ->options(
                        Site::where('tenant_id', Filament::getTenant()->id)
                            ->pluck('name', 'id')
                    ),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'],  fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([25, 50, 100])
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateHeading('No activity yet')
            ->emptyStateDescription('Actions taken by you and your team will appear here.');
    }
}
