<?php

namespace App\Livewire\Filament\Dashboard;

use App\Services\TenantManager;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;


use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class TenantSettings extends Component implements HasForms
{
    use InteractsWithForms;

    private TenantManager $tenantManager;

    public ?array $data = [];

    public function render()
    {
        return view('livewire.filament.dashboard.tenant-settings');
    }

    public function boot(TenantManager $tenantManager): void
    {
        $this->tenantManager = $tenantManager;
    }

    public function mount(): void
    {
        $tenant = Filament::getTenant();
        $this->form->fill([
            'tenant_name' => $tenant->name,
            'enable_slack' => $tenant->enable_slack,
            'slack_webhook' => $tenant->slack_webhook,
            'enable_email' => $tenant->enable_email,
            'email' => $tenant->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General workspace settings')
                ->description('These setting affect only your current active workspace')
                ->schema([
                    TextInput::make('tenant_name')
                    ->label(__('Workspace Name'))
                    ->helperText(__('Edit the name of your workspace'))
                    ->required(),

                ]),
                
                Section::make('Notification Channels')
                    ->description('Set preferred channels for notification alerts')
                    ->schema([
                        Toggle::make('enable_slack')
                            ->live()
                            ->label(__('Enable Slack Notifications')),
                        TextInput::make('slack_webhook')
                            ->label(__('Slack URL Webhook'))
                            ->visible(fn(\Filament\Forms\Get $get):bool => $get('enable_slack'))
                            ->helperText(__('Enter your slack URL webhook')), 
                        Toggle::make('enable_email')
                            ->live()
                            ->label(__('Enable Email Notifications')),
                        TextInput::make('email')
                            ->label(__('Email'))
                            ->visible(fn(\Filament\Forms\Get $get):bool => $get('enable_email'))
                            ->helperText(__('Main email for workspace notificaitons')),      
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->tenantManager->updateTenantData(Filament::getTenant(), $data );

        Notification::make()
            ->title(__('Workspace Settings Saved'))
            ->success()
            ->send();
    }
}
