<?php

namespace App\Livewire\Filament\Dashboard;

use App\Services\TenantManager;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;


use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
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
            'avatar' => $tenant->avatar,
            'enable_slack' => $tenant->enable_slack,
            'slack_webhook' => $tenant->slack_webhook,
            'slack_webhook_deployments' => $tenant->slack_webhook_deployments,
            'enable_email' => $tenant->enable_email,
            'email' => $tenant->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('General workspace settings')
                ->description('These setting affect only your current active workspace')
                ->schema([
                    TextInput::make('tenant_name')
                    ->label(__('Workspace Name'))
                    ->helperText(__('Edit the name of your workspace'))
                    ->required(),
                    // Add small avatar upload
                    FileUpload::make('avatar')
                        ->label(__('Workspace Avatar'))
                        ->uploadingMessage('Uploading avatar...')
                        ->helperText(__('Upload a small avatar for your workspace'))
                        ->image()
                        ->avatar()
                        ->disk('public')
                        ->directory('tenant-avatars')
                        ->imageEditor()
                        ->imageEditorAspectRatios([1, 1])

                ]),
                
                Section::make('Notification Channels')
                    ->description('Set preferred channels for notification alerts')
                    ->schema([
                        Toggle::make('enable_slack')
                            ->live()
                            ->label(__('Enable Slack Notifications')),
                        TextInput::make('slack_webhook')
                            ->label(__('Alerts Webhook URL'))
                            ->placeholder('https://hooks.slack.com/services/...')
                            ->visible(fn(\Filament\Forms\Get $get):bool => $get('enable_slack'))
                            ->helperText(__('Primary channel — receives uptime alerts, SSL warnings, backup notifications, and deployments.')),
                        TextInput::make('slack_webhook_deployments')
                            ->label(__('Deployments Webhook URL (optional)'))
                            ->placeholder('https://hooks.slack.com/services/...')
                            ->visible(fn(\Filament\Forms\Get $get):bool => $get('enable_slack'))
                            ->helperText(__('Separate channel for deployment notifications. If empty, deployments go to the alerts channel above.')),
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
