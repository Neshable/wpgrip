<?php

namespace App\Filament\Dashboard\Resources\RepositoryResource\Pages;

use App\Filament\Dashboard\Resources\RepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Filament\Forms;
use Filament\Forms\Form;

use Filament\Forms\Components\Section;

class EditRepository extends EditRecord
{
    protected static string $resource = RepositoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                        ->helperText('Friendly name') 
                        ->maxLength(255) 
                        ->required(),
                        Forms\Components\Select::make('provider')
                        ->label('Provider')
                        ->required()
                        ->options([
                            'bitbucket' => 'BitBucket',
                            'github' => 'GitHub'
                        ]),
                    ]),
                Section::make('Webhook secret')
                    ->description(__('Paste your generated secret from the GIT service in order for webhooks to trigger deploy.'))
                    ->schema([
                        Forms\Components\TextInput::make('secret')
                        ->label('Secret Token')
                        ->maxLength(255),
                    ]),

                Section::make('Webhook Deployment')
                    ->description('Add this URL to your git webhooks to enable automatic deployments when you push your changes to your current branch.')
                    ->schema([
                        Forms\Components\TextInput::make('webhook_url')
                            ->label('Webhook URL')
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $record) {
                                $component->state('https://app.wpgrip.com/webhook/git/' . $record->webhook);
                            })
                            ->disabled()
                            ->dehydrated(false)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('copy')
                                    ->icon('heroicon-m-clipboard')
                                    ->action(function ($livewire, $state) {
                                        $livewire->js('window.navigator.clipboard.writeText("'.$state.'"); $tooltip("Copied to clipboard", { timeout: 1500 });');
                                    })
                            ),
                    ]),


            ]);
    }
}
