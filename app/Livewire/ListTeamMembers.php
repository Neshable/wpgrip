<?php

namespace App\Livewire;

use App\Models\User;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use Filament\Forms;
use Filament\Forms\Form;

use Filament\Tables\Actions\CreateAction;

use Filament\Facades\Filament;

class ListTeamMembers extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::whereHas('teams', function ($query) {
                $query->where('teams.id', Filament::getTenant()->id );
            }))
            ->columns([
                TextColumn::make('name')
                ->description(function (User $record) {
                    $roles = $record->getRoleNames();
  
                    if ( !empty( $roles ) )
                    {
                        return $roles->implode(', '); // convert collection to string
                    }
                    else 
                    {
                        return 'Owner';
                    }
                   
                } ),
                TextColumn::make('email'),
    
            ])
            ->filters([
                // ...
            ])
            ->heading('Team members')
            ->description('Add or remove team members.')
            ->headerActions([
                // CreateAction::make()
                //     // ->model( Repository::class )
                //     ->label('Add user')
                //     ->tooltip('Connect a site to this repository.')
                //     ->form([
                //             // Forms\Components\Select::make('user_id')
                //             //     ->label('Select User')
                //             //     ->searchable()
                //             //     // ->relationship(
                //             //     //     name: 'site',
                //             //     //     modifyQueryUsing: fn () => Site::where('team_id', Filament::getTenant()->id),
                //             //     // ),
                //             //     //->options(User::all()->pluck('name', 'id')),
                //             //     // This also excludes the current auth user.
                //             //     ->options(
                //             //         User::where('id', '<>', auth()->user()->id)
                //             //             ->pluck('name', 'id')
                //             //     ),
                //             // Forms\Components\Select::make('role')
                //             //     ->label('Select Role')
                //             //     ->searchable()
                //             //     ->options([
                //             //         'draft' => 'Draft',
                //             //         'reviewing' => 'Reviewing',
                //             //         'published' => 'Published',
                //             //     ])
                //             //     ->native(false)
                //     ])
                //     ->createAnother(false)
                //     ->before(function (array $data) {
                //        // dd($data);
                //         // Runs before the form fields are saved to the database.
                //     })
                //     ->action(function (array $data ): ?Repository  {
                //         if ( $this->repo_model && $this->repo_model->id )
                //         {   
                //             // Otherwise attach the plugin in the pivot table with the attributes.
                //             return $this->repo_model->sites()->attach( $data['site_id'], array(
                //                 'branch' => $data['branch'],
                //                 'path' => $data['path'],
                //             ) );
                            
                //         }

                //         return false;
                //     })
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // .....
            ]);
    }

    public function render()
    {
        return view('livewire.list-team-members');
    }
}
