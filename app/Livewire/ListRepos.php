<?php

namespace App\Livewire;
 
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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


use Filament\Tables\Columns\IconColumn;

use App\Filament\App\Resources\RepositoryResource\Pages;
use App\Filament\App\Resources\RepositoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;

use Filament\Tables\Actions\CreateAction;


use Filament\Notifications\Notification;

use App\Jobs\Git\SshAndGitPull;
use App\Jobs\Git\SshAndGitStatus;

use Filament\Infolists\Components\Actions;
use Filament\Tables\Actions\Action;

use Filament\Facades\Filament;


 
class ListRepos extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $site_model;
    
    public function table(Table $table): Table
    {
        return $table
            // ->query(Repository::query())
            ->relationship( fn (): BelongsToMany => $this->site_model->repositories() )
            ->paginated(false)
            ->columns([
                // Tables\Columns\TextColumn::make('status')
                // ->badge()
                // ->color(fn (string $state): string => match ($state) {
                //     'draft' => 'gray',
                //     'problems' => 'warning',
                //     'active' => 'success',
                //     'disconnected' => 'danger',
                //     default =>'success'
                // }),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pivot.path')
                    ->label('Server Path')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('pivot.branch')
                    ->label('Branch')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('provider')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'bitbucket' => 'icon-bitbucket',
                        'github' => 'icon-github'
                    }),

                Tables\Columns\TextColumn::make('last_pull')
                    ->dateTime()
                    ->sortable()
                    ->since(),        
         
            ])
            ->recordUrl(
                fn (Repository $repository): string => route('filament.dashboard.resources.repositories.view', [
                    'record' => $repository,
                    'tenant'    => Filament::getTenant()
                ]),
            )
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction::make()
                //     ->model( Repository::class )
                //     ->label('Add New Repository')
                //     ->tooltip('Assign new repository to your wordpress site.')
                //     ->form([
                //             Forms\Components\TextInput::make('name')
                //                 ->label('Friendly Name')
                //                 ->maxLength(255)
                //                 ->alpha(),
                //             Forms\Components\TextInput::make('path')
                //                 ->label('Absolute Server Path')
                //                 ->maxLength(255),
                //             Forms\Components\TextInput::make('remote')
                //                 ->label('GIT Remote URL')
                //                 ->maxLength(255),
                //             Forms\Components\TextInput::make('branch')
                //                 ->label('Remote Branch to Track')
                //                 ->maxLength(255),
                //             Forms\Components\Radio::make('provider')
                //                 ->label('Provider')
                //                 ->options([
                //                     'bitbucket' => 'BitBucket',
                //                     'github' => 'GitHub'
                //                 ]),
                //             // Forms\Components\TextInput::make('provider')
                //             //     ->maxLength(255),
                //                 // ->enum(MyStatus::class),
                        
                //         // Wizard::make([
                //         //     Wizard\Step::make('Repository Information')
                //         //         ->afterValidation(function () {
                                   
                //         //         })
                //         //         ->beforeValidation(function () {
                //         //             // Notification::make()
                //         //             // ->title('Before validation')
                //         //             // ->success()
                //         //             // ->send();
                //         //         })
                //         //         // ->icon('heroicon-m-shopping-bag')
                //         //         ->description('Get your repo details in place.')
                //         //         ->schema([
                //         //             Forms\Components\TextInput::make('name')
                //         //                 ->label('Friendly Name')
                //         //                 ->maxLength(255)
                //         //                 ->alpha(),
                //         //             Forms\Components\TextInput::make('path')
                //         //                 ->label('Absolute Server Path')
                //         //                 ->maxLength(255),
                //         //             Forms\Components\TextInput::make('remote')
                //         //                 ->label('GIT Remote URL')
                //         //                 ->maxLength(255),
                //         //             Forms\Components\TextInput::make('branch')
                //         //                 ->label('Remote Branch to Track')
                //         //                 ->maxLength(255),
                //         //             Forms\Components\Radio::make('provider')
                //         //                 ->label('Provider')
                //         //                 ->options([
                //         //                     'bitbucket' => 'BitBucket',
                //         //                     'github' => 'GitHub'
                //         //                 ]),
                //         //             // Forms\Components\TextInput::make('provider')
                //         //             //     ->maxLength(255),
                //         //                 // ->enum(MyStatus::class),
                //         //         ]),
                //         //     Wizard\Step::make('Delivery')
                //         //         ->schema([
                //         //             // ...
                //         //         ]),
                //         //     Wizard\Step::make('Billing')
                //         //         ->schema([
                //         //             // ...
                //         //         ]),
                //         // ])->columnSpan('full')
                //         // ->persistStepInQueryString()
                //         // ->submitAction(new HtmlString('<button type="submit">Submit</button>'))
                        
                //         // Forms\Components\TextInput::make('webhook')
                //         //     ->maxLength(255),
                //         // Forms\Components\TextInput::make('checksum')
                //         //     ->maxLength(255),
                //         // Forms\Components\TextInput::make('remote')
                //         //     ->maxLength(255)->activeUrl(),
                //         // Forms\Components\TextInput::make('branch')
                //         //     ->maxLength(255),
                //         // Forms\Components\DatePicker::make('last_pull'),
                //     ])
                //     ->before(function (array $data) {
                //         // dd($data);
                //         // Runs before the form fields are saved to the database.
                //     })
                //     ->using(function (array $data, string $model): ?Repository  {
                //         if ( $this->site_model->id )
                //         {   
                //             // For this site!
                //             $data['site_id'] = $this->site_model->id;
                //             return $model::create($data);
                //         }

                //         return false;
                //     })
            ])
            ->actions([
               
                // Tables\Actions\ActionGroup::make([
                //     Action::make('pull')
                //     ->label('Deploy')
                //     ->action(function ( Repository $repository ) {
                //         SshAndGitPull::dispatchSync( $repository );
                //     } )
                //     ->icon('heroicon-o-check-circle')
                //     ->color('success')
                //     ->requiresConfirmation()
                //     ->modalHeading('Pull Repository?')
                //     ->modalDescription('Are you sure you\'d like to sync this repo?')
                //     ->modalSubmitActionLabel('Yes, pull now')
                //     ->tooltip('Pull this repo'),
                //     Tables\Actions\ViewAction::make(),
                //     Tables\Actions\EditAction::make(),
                //     Tables\Actions\DeleteAction::make(),
                //     Action::make('Check')
                //         ->action(function (  Repository $repository ) {
   
                //             SshAndGitStatus::dispatchSync( $repository );
                            
                //             Notification::make()
                //                 ->title('Git pulled!')
                //                 ->success()
                //                 ->send();
                //         } )
                //         ->label('Check connection')
                //         ->tooltip('Pull this repo'),
                // ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading('No repositories found')
            ->emptyStateDescription('You haven\'t added any repositories yet.')
            ->emptyStateActions([
               // Tables\Actions\CreateAction::make(),
            ]);

    }
    
    public function render(): View
    {
        return view('livewire.list-repos');
    }
}
