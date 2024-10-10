<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Actions\Action;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Facades\Filament;

use App\Jobs\Site\SyncAllSitesStats;

use Filament\Notifications\Notification;

class TopMenuActions extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function render()
    {
        return view('livewire.top-menu-actions');
    }

    /**
     * Trigger the global sync
     *
     * @return Action
     */
    public function syncYourSites(): Action
    {
        return Action::make('test')
            ->requiresConfirmation()
            ->icon('heroicon-o-arrow-path')
            ->modalIcon('heroicon-o-arrow-path')
            ->color('success')
            ->modalHeading('Sync all your sites?')
            ->modalDescription('Automated sync has been already scheduled daily, but you can trigger it manually.')
            ->modalSubmitActionLabel('Sync now')
          
            ->action(function (array $arguments) {
                $tenant = Filament::getTenant(); 

                SyncAllSitesStats::dispatch( $tenant->id );

                Notification::make()
                            ->title('The sync process has been started.')
                            ->success()
                            ->body('A notification will be sent when it\'s completed.') 
                            ->send();
            });
    }
}
