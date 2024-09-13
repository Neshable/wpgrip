<?php

namespace App\Livewire;

use App\Filament\Resources\MonitorResource;
use App\Models\BlacklistMonitor;

use Filament\Tables\Actions;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;

use App\Jobs\RemoteDBBackup;
use App\Jobs\Backup\RemoteFilesBackup;
use Filament\Tables\Columns\IconColumn;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Support\HtmlString;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Livewire\Component;



use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;

use App\Services\GripNotifications;

class ListBlacklistMonitors extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $site_id;

    public function table(Table $table): Table
    {   
        return $table
            ->query( BlacklistMonitor::query()->where('site_id', $this->site_id ) )
            ->deferLoading()
            ->columns([
                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->icon(fn (string $state): string => match ($state) {
                        'ok' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'ok' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('detections')
                    ->numeric(
                        decimalPlaces: 0,
                    ),
                TextColumn::make('created_at')->dateTime()->since()->label('Time Checked'),
                TextColumn::make('ip_address')->label('Origin IP'),

            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('view')
                    ->label('View blacklists details')
                    ->action(function ( BlacklistMonitor $repository ) {
                        true;
                    } )
                    ->modalContent(fn (BlacklistMonitor $record): View => view(
                        'site.listing.blacklists',
                        ['listing' => $record->blacklists ],
                    ))
                    ->modalSubmitAction(false)
                    ->modalWidth(MaxWidth::ThreeExtraLarge)
                    // ->modalContentFooter(view('filament.pages.actions.advance'))
            ])
            ->headerActions([
                
            ])
            ->heading('Blacklist Monitor')
            ->description('Our blacklist checker tests and monitors the domain name across 100+ different blacklists')
            ->emptyStateHeading('No checks found')
            ->emptyStateDescription('We haven\'t done any checks yet.')
            ->bulkActions([
                // BulkAction::make('delete')
                //     ->requiresConfirmation()
                //     ->action(fn (Collection $records) => $records->each->delete())
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->helperText(new HtmlString('The URL for this monitor'))
                    ->maxLength(255),
            ]);
    }


    public function render()
    {
        return view('livewire.list-blacklist-monitors');
    }
}
