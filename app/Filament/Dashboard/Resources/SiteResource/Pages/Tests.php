<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
// use Filament\Actions\Action;

use App\Jobs\Tests\LighthouseTest;
use App\Jobs\Tests\VRTTest;

use Filament\Notifications\Notification; 

use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

use Filament\Actions\Action;
use Filament\Forms\Components;

use App\Jobs\Site\TakeHomeScreenshot;
use App\Services\ImageComparision;
use App\Services\External\PageSpeedInsightsService;


class Tests extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected string $view = 'site.single.tests';

  
    public function runLightHouseTest()
    {
        $url = $this->record->url; // The URL to check

        $desktopInsights = PageSpeedInsightsService::fetchInsights($url, 'desktop');
        $this->record->sitemeta->lighthouse_desktop = json_encode($desktopInsights);
        $this->record->sitemeta->save();
    }

    public function runVRTTest()
    {
        VRTTest::dispatch( $this->record );
        Notification::make()
            ->title('VRT test queued.')
            ->success()
            ->body('The visual regression test is running in the background.')
            ->send();
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    // For testing.
    public function generateHome(): Action
    {
        return Action::make('changeLog')   
        ->color('info')
        ->modalHeading('Re-generate home control screenshot')
        ->modalSubmitActionLabel('Proceed')
        // ->modalIcon('heroicon-o-arrow-path')
        ->requiresConfirmation()
        ->modalDescription('If you recreate the current control homescreen screenshot, it may invalidate all previous tests.')
        // ->form([
        //     Components\TextInput::make('error_log_path')
        //         ->label('Absolute error log path')
        //         ->regex('/^\/.+\.log$/i') // Checks if ends with .log and starts with /
        //         ->validationMessages([
        //             'regex' => 'The value doesn\'t seem like an absolute path to a .log file',
        //         ])
        //         ->required(),
        // ]) 
        // ->fillForm(fn (): array => [
        //     'error_log_path' => $this->record->error_log_path
        // ]) 
        ->action(function (array $data ): void {
            // $this->record->error_log_path = trim(htmlspecialchars($data['error_log_path']));
            // $this->record->save();
            // Send some basic notificaiton
           //  GripNotifications::fieldUpdated();
           TakeHomeScreenshot::dispatch( $this->record );
           Notification::make()
               ->title('Screenshot queued.')
               ->success()
               ->body('Control screenshot is being taken in the background.')
               ->send();
        });      
         
    }

 

    public function triggerAction( string $action = 'default' ) 
    {
        switch ( $action ) {
            case 'runLightHouseTest':
                LighthouseTest::dispatch( $this->record );
                Notification::make()
                    ->title('Lighthouse test queued.')
                    ->success()
                    ->body('The Lighthouse test is running in the background.')
                    ->send();
                break;
            case 'backupfiles':
                Jobs\RemoteFilesBackup::dispatch( $this->record );
                    // Save to DB and push notification.
                    Notification::make()
                    ->title('Backup started.')
                    ->success()
                    ->body('Backup job successfully started in the background.') 
                    ->send();
                break;    
            default:
        }
        
    }

}
