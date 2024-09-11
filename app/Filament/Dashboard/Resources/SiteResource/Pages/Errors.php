<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components;

use App\Services\GripNotifications;

use Illuminate\Contracts\View\View;


class Errors extends ViewRecord
{

    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.errors';

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }
  
    public function editLogLocationAction(): Action
    {
        return Action::make('changeLog')   
        ->color('info')
        ->label('Clear cache')
        ->modalHeading('PHP Error log path')
        ->modalSubmitActionLabel('Save')
        // ->modalIcon('heroicon-o-arrow-path')
        ->modalDescription('The path for the PHP error logs differs across various servers. Please input your absolute path to the php error file ( nginx/apache ). ')
        ->form([
            Components\TextInput::make('error_log_path')
                ->label('Absolute error log path')
                ->regex('/^\/.+\.log$/i') // Checks if ends with .log and starts with /
                ->validationMessages([
                    'regex' => 'The value doesn\'t seem like an absolute path to a .log file',
                ])
                ->required(),
        ]) 
        ->fillForm(fn (): array => [
            'error_log_path' => $this->record->error_log_path
        ]) 
        ->action(function (array $data ): void {
            $this->record->error_log_path = trim(htmlspecialchars($data['error_log_path']));
            $this->record->save();
            // Send some basic notificaiton
            GripNotifications::fieldUpdated();
        });
   
    }

}
