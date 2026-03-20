<?php

namespace App\Filament\Dashboard\Resources\RepositoryResource\Pages;

use App\Models\Repository;

use App\Filament\Dashboard\Resources\RepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

use App\Jobs\Git\SshAndGitPull;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ViewRepository extends ViewRecord
{
    protected static string $resource = RepositoryResource::class;

    protected string $view = 'repo.single.overview';

    // public function render(): View
    // {
    //     return view('filament.sites.view-site');
    // }

    public function getHeader(): ?View
    {
        return view('repo.single.header' );
    }

    
    public function getTitle(): string | Htmlable
    {
        return $this->getRecord()->name;
    }

    public static function deployRepo( ?Repository $repository )
    {
        SshAndGitPull::dispatch( $repository );
        Notification::make()
            ->title('Deploy queued.')
            ->success()
            ->body('Git pull is running in the background.')
            ->send();
    }

    // public function getHeader(): ?View
    // {
    //     return view('site.single.header');
    // }


    public function getHeaderActions(): array
    {
        return [
            Actions\Action::make('deploy')
                ->action(function ( Repository $repository ) {
                    SshAndGitPull::dispatch( $repository );
                    Notification::make()
                        ->title('Deploy queued.')
                        ->success()
                        ->body('Git pull is running in the background.')
                        ->send();
                } )
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Deploy repository?')
                ->modalSubheading('Are you sure you\'d like to sync this repo?')
                ->modalButton('Yes, deploy now')
                ->tooltip('Deploy this repo'),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
