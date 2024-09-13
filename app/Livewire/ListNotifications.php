<?php

namespace App\Livewire;
 
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Repository;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Forms\Get;

use App\Models\NotificationChannel;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;

use Filament\Tables\Actions\CreateAction;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Notifications\Notification;

use Filament\Infolists\Components\Actions;
use Filament\Tables\Actions\Action;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ListNotifications extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $repo_model;
    
    public function table(Table $table): Table
    {
        // dd($this->repo_model->sites()->toSql());

        return $table
            ->query( NotificationChannel::query()->where('team_id', Filament::getTenant()->id ) )
            // ->relationship( fn (): BelongsToMany => $this->repo_model->sites() )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('service')
                ->label('Service'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),            
                Tables\Columns\TextColumn::make('webhook')
                    ->label('Content')
                    ->state(fn (NotificationChannel $record): ?string => $record->email ?? $record->webhook)
            ])
            ->filters([
                //
            ])
            ->heading('Notification channels')
            ->description('In this section you can add notifications to alert you about specific events. Notification channels can include Slack and Email.')
            ->headerActions([
                CreateAction::make()
                    // ->model( Repository::class )
                    ->label('Add new')
                    ->tooltip('Add new notification channel')
                    ->hidden( NotificationChannel::query()->where('team_id', Filament::getTenant()->id )->count() > 3 )
                    ->form([
                        Forms\Components\Select::make('service')
                            ->options([
                                'slack' => 'Slack',
                                'telegram' => 'Telegram',
                                'email' => 'Email',
                            ])
                            ->live()
                            ->native(false),
                        Forms\Components\TextInput::make('name')
                        ->label('Friendly Name')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('webhook')
                        ->label('Webhook')
                        ->url()
                        ->required(fn (Get $get): bool => $get('service') === 'slack')
                        ->visible(fn (Get $get): bool => $get('service') === 'slack'),
                        Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(fn (Get $get): bool => $get('service') === 'email')
                        ->visible(fn (Get $get): bool => $get('service') === 'email')           
                    ])
                    ->action(function (array $data ): ?NotificationChannel {
                        // @todo limit.
                        return NotificationChannel::create([
                            'user_id' => auth()->user()->id,
                            'team_id' => Filament::getTenant()->id,
                            'service' => $data['service'],
                            'name' => $data['name'],
                            'webhook' => $data['webhook'] ?? null,
                            'email' => $data['email'] ?? null,
                            
                        ]);

                    })
                    // ->using(function (array $data, string $model): ?NotificationChannel  {
                       

                    //     return false;
                    // })
            ])
            ->actions([
                // Tables\Actions\Action::make('deploy')
                //     ->action(function (  Site $site ) {
                //         SshAndGitPull::dispatchSync( $this->repo_model, $site );
                //     } )
                //     ->icon('heroicon-o-check-circle')
                //     ->color('success')
                //     ->requiresConfirmation()
                //     ->modalHeading('Deploy repository?')
                //     ->modalDescription('Are you sure you\'d like to sync this repo?')
                //     ->modalSubmitActionLabel('Yes, deploy now')
                //     ->tooltip('Deploy this repo'),
                Tables\Actions\ActionGroup::make([  
                    Tables\Actions\DeleteAction::make(),
                    // Action::make('detach')
                    //     ->label('Detach')
                    //     ->action(function ( Site $site ) {
                    //         // Detach the record from the pivot table.
                    //         $this->repo_model->sites()->detach( $site->site_id );
                            
                    //         Notification::make()
                    //             ->title('Site detached.')
                    //             ->success()
                    //             ->send();
                    //     } ),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading('No notification channels found')
            ->emptyStateDescription('You haven\'t added any channels yet.')
            ->emptyStateActions([
               // Tables\Actions\CreateAction::make(),
            ]);

    }
    
    public function render(): View
    {
        return view('livewire.list-notifications');
    }
}
