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


            ]);
    }
}
