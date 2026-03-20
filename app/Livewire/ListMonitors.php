<?php

namespace App\Livewire;

use App\Filament\Resources\MonitorResource;
use App\Models\MonitorLog;
use App\Models\UptimeMonitor;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

use App\Jobs\RemoteDBBackup;
use App\Jobs\Backup\RemoteFilesBackup;
use Filament\Tables\Columns\IconColumn;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Support\HtmlString;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;
use Carbon\Carbon;

use App\Services\GripNotifications;

class ListMonitors extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public $site_id;

    public function table(Table $table): Table
    {   
        return $table
            ->query( UptimeMonitor::query()->where('site_id', $this->site_id ) )
            ->deferLoading()
            ->columns([
                IconColumn::make('uptime_status')
                    ->label('Status')
                    ->boolean()
                    ->tooltip('Uptime status')
                    ->icon(fn (string $state): string => match ($state) {
                        'up' => 'heroicon-o-arrow-trending-up',
                        'down' => 'heroicon-o-arrow-trending-down',
                        default => 'heroicon-o-arrow-trending-down',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'up' => 'success',
                        'down' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('url'),
                IconColumn::make('uptime_check_enabled')
                    ->label('Enabled')
                    ->boolean(),
                TextColumn::make('uptime_check_interval_in_minutes')
                    ->tooltip('Interval check in minutes')
                    ->label('Interval'),
                TextColumn::make('uptime_check_failure_reason')
                    ->label('Errors')
                    ->wrap(),
                TextColumn::make('response_time')
                    ->label('Response')
                    ->getStateUsing(function (UptimeMonitor $record) {
                        $log = MonitorLog::where('site_id', $record->site_id)
                            ->where('url', (string) $record->url)
                            ->whereNotNull('response_time_ms')
                            ->latest()
                            ->first();
                        return $log ? $log->response_time_ms . 'ms' : '—';
                    })
                    ->color(function (UptimeMonitor $record) {
                        $log = MonitorLog::where('site_id', $record->site_id)
                            ->where('url', (string) $record->url)
                            ->whereNotNull('response_time_ms')
                            ->latest()
                            ->first();
                        if (! $log) return 'gray';
                        if ($log->response_time_ms <= 500) return 'success';
                        if ($log->response_time_ms <= 1000) return 'warning';
                        return 'danger';
                    })
                    ->badge(),
                TextColumn::make('uptime_last_check_date')
                    ->label('Last checked')
                    ->since()
                // IconColumn::make('success')
                // ->label('Status')
                // ->boolean(),
                // TextColumn::make('commit')->description(fn (RepoCommit $record): string => $record->message),
                
                // // TextColumn::make('file_path'),
                // // TextColumn::make('site.name')->sortable(),
                // TextColumn::make('committer')->label('Author'),
                // TextColumn::make('branch'),
                // TextColumn::make('created_at')->dateTime()->label('Date'),
                
                // TextColumn::make('message'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    DeleteAction::make(),
                    EditAction::make()
                    ->form([
                        Forms\Components\Select::make('uptime_check_interval_in_minutes')
                        ->label('How often to check?')
                        ->options([
                            2 => '2 minutes', // @todo deactivate for basic plan
                            5 => '5 minutes',
                            15 => '15 minutes',
                            30 => '30 minutes',
                            45 => '45 minutes',
                            60 => '1 hour',
                            120 => '2 hours'
                        ])
                    ])
                    ->successNotificationTitle('Monitor updated'),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->link()
                    ->model( UptimeMonitor::class )
                    ->hidden( UptimeMonitor::query()->where('site_id', $this->site_id )->count() > 3 )
                    ->label('Add a monitor')
                    ->form( [
                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->helperText(new HtmlString('The URL for this monitor'))
                            ->maxLength(255),
                        Forms\Components\Select::make('uptime_check_interval_in_minutes')
                            ->label('How often to check?')
                            ->options([
                                5 => '5 minutes',
                                15 => '15 minutes',
                                30 => '30 minutes',
                                45 => '45 minutes',
                                60 => '1 hour',
                                120 => '2 hours'
                            ])
                    ] )
                    ->createAnother(false)
                    ->before(function (array $data) {
                        // dd($data);
                        // Runs before the form fields are saved to the database.
                    })
                    ->using(function (array $data, string $model): ?UptimeMonitor  {
                        if ( $this->site_id )
                        {   
                            $data['site_id'] = $this->site_id;
                            $data['uptime_check_enabled'] = true;
                            $data['certificate_check_enabled'] = str_starts_with($data['url'] ?? '', 'https');
                            $data['uptime_check_method'] = 'head';
                            $data['look_for_string'] = '';
                            return $model::create($data);
                        }

                        return null;
                    })
                // Action::make('create')
            ])
            ->heading('Monitors')
            ->description('List of all monitored URLs. Current limit is 3 unique URLs per website.')
            ->emptyStateHeading('No monitors found')
            ->emptyStateDescription('You haven\'t done any monitors yet.')
            ->bulkActions([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete())
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->helperText(new HtmlString('The URL for this monitor'))
                    ->maxLength(255),
            ]);
    }


    public function render()
    {
        return view('livewire.list-monitors');
    }
}
