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
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;

use Filament\Actions\CreateAction;


use Filament\Notifications\Notification;

use App\Jobs\Git\SshAndGitPull;
use App\Jobs\Git\SshAndGitStatus;

use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Actions\Action;

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
            Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->sortable(),
            Tables\Columns\TextColumn::make('provider')
                ->badge()
                ->icon(fn (string $state): string => match ($state) {
                    'bitbucket' => 'icon-bitbucket',
                    'github' => 'icon-github'
                }),
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
        
                Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'error' => 'danger',
                    'success' => 'success',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('status_text')
                ->label('Last Status Log')
                ->sortable()
                ->wrap()
                ->limit(50),
            
            Tables\Columns\TextColumn::make('last_pull')
                ->dateTime()
                ->label('Last Activity')
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
                // Actions\Action::make('view_repositories')
                //     ->label('View All Repositories')
                //     ->icon('heroicon-m-code-bracket')
                //     ->url(fn (): string => route('filament.dashboard.resources.repositories.index', [
                //         'tenant' => Filament::getTenant()
                //     ]))
                //     ->openUrlInNewTab(),
            ])
            ->actions([
                Actions\Action::make('deploy')
                ->action(function (Repository $repository) {
                    // Update the pivot status to WORKING
                    $this->site_model->repositories()
                        ->updateExistingPivot($repository->id, [
                            'status' => \App\Enums\RepoStatus::WORKING->value
                        ]);
                        
                    SshAndGitPull::dispatch($repository, $this->site_model );
                })
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Deploy repository?')
                ->modalDescription('Are you sure you\'d like to sync this repo?')
                ->modalSubmitActionLabel('Yes, deploy now')
                ->tooltip('Deploy this repo'),
            ])
            ->bulkActions([
                // Actions\BulkActionGroup::make([
                //     Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading('No repositories found')
            ->emptyStateDescription('You haven\'t added any repositories yet.')
            ->emptyStateActions([
               // Actions\CreateAction::make(),
            ]);

    }
    
    public function render(): View
    {
        return view('livewire.list-repos');
    }
}
