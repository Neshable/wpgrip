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

use App\Models\Site;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;

use Filament\Tables\Actions\CreateAction;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Notifications\Notification;

use App\Jobs\Git\SshAndGitPull;
use App\Jobs\Git\SshAndGitStatus;

use Filament\Infolists\Components\Actions;
use Filament\Tables\Actions\Action;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ListRepoSites extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $repo_model;
    
    public function table(Table $table): Table
    {
        // dd($this->repo_model->sites()->toSql());

        return $table
            // ->query(Repository::query())
            ->relationship( fn (): BelongsToMany => $this->repo_model->sites() )
            ->paginated(false)
            ->columns([
            Tables\Columns\ViewColumn::make('name')
                ->view('filament.tables.columns.sitename'),
            // Tables\Columns\TextColumn::make('url')
            //     ->label('URL'),
            Tables\Columns\TextColumn::make('path')
                ->label('Path'),
            Tables\Columns\IconColumn::make('is_active')
                ->label('Active')
                ->boolean(),
            Tables\Columns\TextColumn::make('branch')
                ->label('Branch'),
            Tables\Columns\ToggleColumn::make('auto_deploy')
                ->label('Auto Deploy')
                // ->updateState(function ($state) {
                //     dd($state);
                // })
                ->updateStateUsing(function ($record, $state) {
                    // dd($record->id);
                    // $record->auto_deploy = $state;
                    // $record->save();

                    $this->repo_model->sites()->updateExistingPivot($record->id, [
                        'auto_deploy' => $state,
                    ]);

                }),
            Tables\Columns\TextColumn::make('last_pull')
                            ->dateTime()
                            ->label('Last deploy')
                            ->sortable()
                            ->since(),
            ])
            ->filters([
                //
            ])
            ->heading('Connected sites')
            ->description('List of all sites where the repo is used.')
            ->headerActions([
                CreateAction::make('addsite')
                    // ->model( Repository::class )
                    ->label('Connect a site')
                    ->tooltip('Connect a site to this repository.')
                    ->form([                
                            Forms\Components\Select::make('site_id')
                                ->label('Choose a site')
                                ->helperText('You can select production or staging site.')
                                ->searchable()
                                // ->relationship(
                                //     name: 'site',
                                //     modifyQueryUsing: fn () => Site::where('tenant_id', Filament::getTenant()->id),
                                // ),
                                ->options(
                                    Site::where('tenant_id', Filament::getTenant()->id)
                                        ->whereNotIn('sites.id', $this->repo_model->sites()->pluck('sites.id')->toArray())
                                        ->pluck('name', 'sites.id')
                                ),
                            Forms\Components\TextInput::make('path')
                                ->label('Relative path to deploy')
                                ->helperText('Make sure the directory doesn\'t exist. It will be created with the first deploy. This is relative path inside your WordPress installation.')
                                ->placeholder('e.g. wp-content/themes/your-theme or wp-content/plugins/your-plugin')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('branch')
                                ->label('Branch to use')
                                ->helperText('Usually master or main, but you can use any of the existing branches')
                                ->maxLength(255),
                            
                    ])
                    ->modalHeading('Connect a Site to This Repository')
                    ->modalSubmitActionLabel('Connect Site')
                    ->createAnother( false )
                    ->before(function (array $data) {
                       // dd($data);
                        // Runs before the form fields are saved to the database.
                    })
                    ->action(function (array $data ): ?Repository  {
                        if ( $this->repo_model && $this->repo_model->id )
                        {   
                            // Otherwise attach the plugin in the pivot table with the attributes.
                            return $this->repo_model->sites()->attach( $data['site_id'], array(
                                'branch' => $data['branch'],
                                'is_active' => 0,
                                'path' => $data['path'],
                            ) );
                            
                        }

                        return false;
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('deploy')
                    ->action(function ( Site $site ) {
                        SshAndGitPull::dispatchSync( $this->repo_model, $site );
                    } )
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Deploy repository?')
                    ->modalDescription('Are you sure you\'d like to sync this repo?')
                    ->modalSubmitActionLabel('Yes, deploy now')
                    ->tooltip('Deploy this repo'),
                Tables\Actions\ActionGroup::make([  
                    // Tables\Actions\DeleteAction::make(),
                    Action::make('detach')
                        ->label('Remove')
                        ->requiresConfirmation()
                        ->modalHeading('Remove repository?')
                        ->modalDescription('After this action the repository will remain on the server but will be deleted from here.')
                        ->modalSubmitActionLabel('Yes, detach')
                        ->action(function ( Site $site ) {
                            // Detach the record from the pivot table.
                            $this->repo_model->sites()->detach( $site->site_id );
                            
                            Notification::make()
                                ->title('Site detached.')
                                ->success()
                                ->send();
                        } ),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading('No sites found')
            ->emptyStateDescription('You haven\'t added any sites yet.')
            ->emptyStateActions([
               // Tables\Actions\CreateAction::make(),
            ]);

    }
    
    public function render(): View
    {
        return view('livewire.list-repo-sites');
    }
}
