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
use Filament\Tables;
use Filament\Actions;

use Filament\Forms;
use Filament\Schemas\Schema;

use Filament\Actions\CreateAction;

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
                // CreateAction::make(),
                // Actions\Action::make('triggerBackup')
                //     ->link()
                //     ->requiresConfirmation(),
                
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
