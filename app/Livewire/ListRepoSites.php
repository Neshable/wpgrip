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
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;

use Filament\Actions\CreateAction;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

use Filament\Notifications\Notification;

use App\Jobs\Git\SshAndGitPull;
use App\Jobs\Git\SshAndChangeBranch;
use App\Jobs\Git\SshAndGitStatus;
use App\Jobs\Git\SshAndGitRevert;
use App\Models\Deployment;
use Illuminate\Support\Str;

use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Actions\Action;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Enums\RepoStatus;

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
            ->poll('20s')
            ->deferLoading()
            ->columns([
            Tables\Columns\ImageColumn::make('')
                ->width(35)
                ->height(35)
                ->defaultImageUrl(url('/images/wordpress.svg')),
            Tables\Columns\TextColumn::make('name')
                ->view('filament.tables.columns.sitename'),
            Tables\Columns\TextColumn::make('pivot.path')
                ->label('Path')
                ->formatStateUsing(function ($record) {
                    $branchIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>';
                    return $record->pivot->path . '<br><span class="text-gray-500"><strong>Branch:</strong> ' . $branchIcon . $record->pivot->branch . '</span>';
                })
                ->html()
                ->sortable(),
  
            Tables\Columns\TextColumn::make('pivot.status')
                ->label('Status')
                ->view('filament.tables.columns.status-with-loader'),
            Tables\Columns\TextColumn::make('status_text')
                ->label('Last Status Log')
                ->sortable()
                ->wrap()
                ->limit(80)
                ->tooltip(fn ($record) => $record->pivot->status_text),
            Tables\Columns\ToggleColumn::make('auto_deploy')
                ->label('Push to Deploy')
                // ->updateState(function ($state) {
                //     dd($state);
                // })
                ->updateStateUsing(function ($record, $state) {
    
                    $this->repo_model->sites()->updateExistingPivot($record->id, [
                        'auto_deploy' => $state,
                    ]);

                }),
            Tables\Columns\TextColumn::make('last_pull')
                    ->dateTime()
                    ->label('Last Activity')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->heading('Connected sites')
            ->description('List of all sites where the repository is connected to. You can connect more sites to the same repository.')
            ->headerActions([
                CreateAction::make('addsite')
                    // ->model( Repository::class )
                    ->label('Connect a site')
                    ->tooltip('Connect a site to this repository.')
                    ->form([     
                            Forms\Components\ViewField::make('git_ssh_key_instructions')
                                ->view('filament.forms.components.git-ssh-key-instructions')
                                ->columnSpanFull()
                                ->dehydrated(false),
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
                                'status' => RepoStatus::PENDING->value,
                                'path' => $data['path'],
                            ) );
                            
                        }

                        return false;
                    })
            ])
            ->actions([
                Actions\Action::make('deploy')
                    ->action(function ( Site $site ) {
                        // Update the pivot status to WORKING
                        $this->repo_model->sites()
                            ->updateExistingPivot($site->id, [
                                'status' => \App\Enums\RepoStatus::WORKING->value
                            ]);
                        
                        SshAndGitPull::dispatch( $this->repo_model, $site );
                    } )
                    ->label('Deploy')
                    ->icon('heroicon-o-arrow-up-on-square-stack')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Deploy repository?')
                    ->modalDescription('Are you sure you\'d like to sync this repo?')
                    ->modalSubmitActionLabel('Yes, deploy now')
                    ->tooltip('Deploy this repo'),
                Actions\ActionGroup::make([  
                    // Actions\DeleteAction::make(),
                    Action::make('change_branch')
                        ->label('Change Branch')
                        ->color('info')
                        ->icon('heroicon-o-arrow-turn-down-right')
                        ->requiresConfirmation()
                        ->modalHeading('Change active branch?')
                        ->modalSubmitActionLabel('Change')
                        ->form([                
                            Forms\Components\TextInput::make('branch')
                                ->label('New branch to use')
                                ->helperText('Usually master or main, but you can use any of the existing branches')
                                ->maxLength(255),     
                        ])
           
                    // ->action(function ( Site $site ) {
                    //     // Update the pivot status to WORKING
                    //     $this->repo_model->sites()
                    //         ->updateExistingPivot($site->id, [
                    //             'status' => \App\Enums\RepoStatus::WORKING->value
                    //         ]);
                        
                    //     SshAndGitPull::dispatch( $this->repo_model, $site );
                    // } )

                    ->action(function (array $data,  Site $site )  {      
                        // Update the pivot status to WORKING
                        $this->repo_model->sites()
                            ->updateExistingPivot($site->id, [
                                'status' => \App\Enums\RepoStatus::WORKING->value
                            ]);
                        
                        SshAndChangeBranch::dispatch( $this->repo_model, $site, $data['branch'] );
                    }),
                    Action::make('revert_commit')
                        ->label('Revert Commit')
                        ->color('warning')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->modalHeading('Revert to a previous commit')
                        ->modalDescription('This will hard-reset the deployed code on the server to the selected commit. The remote repository is not affected.')
                        ->modalSubmitActionLabel('Revert')
                        ->form([
                            Forms\Components\Select::make('commit_hash')
                                ->label('Select commit to revert to')
                                ->options(function (Site $record) {
                                    $options = [];
                                    $deployments = Deployment::where('pivot_id', $record->pivot->id)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();
                                    foreach ($deployments as $d) {
                                        $label = substr($d->commit, 0, 8) . ' – ' . Str::limit($d->message, 40) . ' (' . $d->created_at->diffForHumans() . ')';
                                        $options[$d->commit] = $label;
                                    }
                                    $options['custom'] = 'Custom commit hash…';
                                    return $options;
                                })
                                ->live()
                                ->required(),
                            Forms\Components\TextInput::make('custom_commit_hash')
                                ->label('Custom commit hash')
                                ->placeholder('Full or short commit hash (e.g. abc1234)')
                                ->visible(fn ($get) => $get('commit_hash') === 'custom'),
                        ])
                        ->action(function (array $data, Site $site) {
                            $hash = $data['commit_hash'] === 'custom'
                                ? trim($data['custom_commit_hash'])
                                : $data['commit_hash'];

                            $this->repo_model->sites()->updateExistingPivot($site->id, [
                                'status' => \App\Enums\RepoStatus::WORKING->value,
                            ]);

                            SshAndGitRevert::dispatch($this->repo_model, $site, $hash);
                        }),
                    Action::make('detach')
                        ->label('Remove')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Remove repository?')
                        ->icon('heroicon-o-trash')
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
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make('bulk_detach')
                        ->label('Remove')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($site) {
                                $this->repo_model->sites()->detach($site->site_id);
                            });
                            
                            Notification::make()
                                ->title('Sites detached.')
                                ->success()
                                ->send();
                        }),
                    Actions\BulkAction::make('bulk_deploy')
                        ->label('Deploy')
                        ->color('success')
                        ->icon('heroicon-o-arrow-up-on-square-stack')
                        ->requiresConfirmation()
                        ->modalHeading('Deploy repositories?')
                        ->modalDescription('Are you sure you\'d like to sync these repositories?')
                        ->modalSubmitActionLabel('Yes, deploy now')
                        ->action(function (Collection $records) {
                            $records->each(function ($site) {
                          
                                // Update the pivot status to WORKING
                                $this->repo_model->sites()
                                    ->updateExistingPivot($site->id, [
                                        'status' => \App\Enums\RepoStatus::WORKING->value
                                    ]);
                                
                                SshAndGitPull::dispatch($this->repo_model, $site);
                            });
                        }),
                       
                ]),
            ])
            ->emptyStateHeading('No sites found')
            ->emptyStateDescription('You haven\'t added any sites yet.')
            ->emptyStateActions([
               // Actions\CreateAction::make(),
            ]);

    }
    
    public function render(): View
    {
        return view('livewire.list-repo-sites');
    }
}
