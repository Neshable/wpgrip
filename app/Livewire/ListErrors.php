<?php

namespace App\Livewire;

use App\Models\UptimeMonitor;
use App\Models\PHPLog;
use App\Models\Site;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

use App\Jobs\Server\TailErrorLog;
use Filament\Notifications\Notification;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
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

use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ListErrors extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public $site_id;

    public function table(Table $table): Table
    {   
        return $table
            ->query( 
                PHPLog::query()
                ->where('site_id', $this->site_id )
                ->orderBy('created_at', 'desc')
            )
            ->deferLoading()
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->since(),
                TextColumn::make('type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray',
                    'parse' => 'warning',
                    'fatal' => 'danger',
                    default => 'gray'
                }),
                TextColumn::make('message')
                ->copyable()
                ->searchable()
                ->copyMessage('Error message copied.')
                ->copyMessageDuration(1500)
                ->wrap(),     
            ])
            ->filters([
                SelectFilter::make('type')
                ->multiple()
                ->options([
                    'parse' => 'Parse errors',
                    'fatal' => 'Fatal errors',
                ])
                ->attribute('type'),
                Filter::make('created_at')
                    ->label('Specify date range')
                    ->form([
                        DatePicker::make('created_from')
                        ->label('From'),
                        DatePicker::make('created_until')
                        ->label('To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            // ->actions([
            //     ActionGroup::make([
            //         DeleteAction::make(),
            //         EditAction::make()
            //         ->form([
            //             Forms\Components\Select::make('uptime_check_interval_in_minutes')
            //             ->label('How often to check?')
            //             ->options([
            //                 2 => '2 minutes', // @todo deactivate for basic plan
            //                 5 => '5 minutes',
            //                 15 => '15 minutes',
            //                 30 => '30 minutes',
            //                 45 => '45 minutes',
            //                 60 => '1 hour',
            //                 120 => '2 hours'
            //             ])
            //         ])
            //         ->successNotificationTitle('Monitor updated'),
            //     ])->icon('heroicon-m-ellipsis-horizontal'),
            // ])
            ->headerActions([
                Action::make('clear')
                ->label('Sync logs')
                ->requiresConfirmation()
                ->action(function () {
                    TailErrorLog::dispatch( Site::find( $this->site_id ), 'fatal' );
                    Notification::make()->title('Log sync queued.')->success()->body('Error logs are being fetched in the background.')->send();
                }),
                
                Action::make('delete')
                ->label('Delete logs')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => PHPLog::query()->where('site_id', $this->site_id )->delete())
            ])
            ->heading('Errors')
            ->description('List of all recorded PHP errors. Limited to 100 records per site.')
            ->emptyStateHeading('No errors found')
            ->emptyStateDescription('We haven\'t detected any issues so far.')
            ->bulkActions([
                // BulkAction::make('delete')
                //     ->requiresConfirmation()
                //     ->action(fn (Collection $records) => $records->each->delete())
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
        return view('livewire.list-errors');
    }
}

