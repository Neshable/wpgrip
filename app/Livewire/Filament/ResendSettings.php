<?php

namespace App\Livewire\Filament;

use App\Services\ConfigManager;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Livewire\Component;

class ResendSettings extends Component implements HasForms
{
    private ConfigManager $configManager;

    protected string $slug = 'resend';

    use InteractsWithForms;

    public ?array $data = [];

    public function boot(ConfigManager $configManager): void
    {
        $this->configManager = $configManager;
    }

    public function render()
    {
        return view('livewire.filament.resend-settings');
    }

    public function mount(): void
    {
        $this->form->fill([
            'key' => $this->configManager->get('services.'.$this->slug.'.key'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('key')
                            ->label(__('Key'))
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->configManager->set('services.'.$this->slug.'.key', $data['key']);

        Notification::make()
            ->title(__('Settings Saved'))
            ->success()
            ->send();
    }
}
