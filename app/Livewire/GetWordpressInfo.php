<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

use App\Models\Site;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Livewire\Attributes\On; 

use App\Enums\SiteStatus;

use App\Jobs\Updates\UpdateWPCore;

class GetWordpressInfo extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $data;
    public $loading = true;
    public $site;

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $response = Http::get('https://api.wordpress.org/core/version-check/1.6/');

        if (!$response->ok()) {
            // Handle the error
            $this->loading = false;
            return;
        }

        $this->data = unserialize($response->body());
        $this->loading = false;
    }
    /**
     * Fetch the latest versions from the official codex
     *
     * @return array
     */
    public function fetchWPVersions()
    {
        $versions = [];

        $response = Http::get('https://api.wordpress.org/core/version-check/1.7/');
    
        if (!$response->ok()) {
            // Handle the error
            $this->loading = false;
            return;
        }
    
        $data = $response->json();
        
        // Loop through the offers and extract needed fields
        foreach ($data['offers'] as $offer) {
            // Format the value string
            $value = $offer['version'] . " (Min PHP ver - " . $offer['php_version'] . " / Min MySQL ver " . $offer['mysql_version'] . ")";
    
            $versions[$offer['version']] = $value;
        }
    
        $this->loading = false;
    
        return $versions;
    }


    public function updateAction(): Action
    {
        return Action::make('update')
        ->requiresConfirmation()
        ->color('success')
        ->form([
            Select::make('version')
                ->label('Choose WP Version')
                ->options( $this->extractWordpressVersion() )
                ->required(),
        ])
        ->modalHeading('Update WP Core')
        ->modalDescription('Are you sure you\'d like to udpate the core version?')
        ->modalSubmitActionLabel('Update now')
        ->action(function (array $data, array $arguments ): void {
            $site = Site::find($arguments['site_id']);
            // This should go to event.
            $site->status = SiteStatus::UpdatingCore;
            $site->save();

            UpdateWPCore::dispatch( $site, $data['version'] );
        });
    }

    public function downgradeAction(): Action
    {
        return Action::make('downgrade')
        ->requiresConfirmation()
        ->color('warning')
        ->form([
            Select::make('version')
                ->label('Choose WP Version')
                ->options( $this->fetchWPVersions() )
                ->required(),
        ])
        ->modalHeading('Downgrade WP Core')
        ->modalDescription('Are you sure you\'d like to downgrade the core version?')
        ->modalSubmitActionLabel('Downgrade now')
        ->action(function (array $data, array $arguments ): void {
            $site = Site::find($arguments['site_id']);
            // This should go to event.
            $site->status = SiteStatus::UpdatingCore;
            $site->save();

            UpdateWPCore::dispatch( $site, $data['version'], true );
        });
    }

    #[On('site-updated')] 
    public function updatePostList($title)
    {
        // ...
    }

    public function extractWordpressVersion()
    {
        $options = array();

        if ( $this->data && is_array( $this->data ) )
        {
            $options[ $this->data['offers'][0]['current'] ] = $this->data['offers'][0]['current'];
        }

        return $options;
    }


    public function render()
    {
        return view('livewire.get-wordpress-info');
    }
}
