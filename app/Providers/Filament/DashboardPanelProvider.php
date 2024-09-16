<?php

namespace App\Providers\Filament;

use App\Constants\TenancyPermissionConstants;
use App\Filament\Dashboard\Pages\TenantSettings;
use App\Models\Tenant;
use App\Services\TenantPermissionManager;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

use App\Filament\Dashboard\Resources\OrderResource;
use App\Filament\Dashboard\Resources\TransactionResource;
use App\Filament\Dashboard\Resources\SubscriptionResource;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('dashboard')
            ->path('dashboard')
            ->spa()
            ->spaUrlExceptions([
                '*/admin/*',
            ])
            ->darkMode(false)
            ->unsavedChangesAlerts()
            ->maxContentWidth('screen-2xl')
            // ->sidebarFullyCollapsibleOnDesktop()
            ->userMenuItems([
                MenuItem::make()
                    ->label(__('Admin Panel'))
                    ->visible(
                        fn () => auth()->user()->isAdmin()
                    )
                    ->url(fn () => route('filament.admin.pages.dashboard'))
                    ->icon('heroicon-s-cog-8-tooth'),
                MenuItem::make()
                    ->label('Orders')
                    ->url(fn (): string => OrderResource::getUrl())
                    ->icon('heroicon-o-rectangle-stack'),
                MenuItem::make()
                    ->label('Transactions')
                    ->url(fn (): string => TransactionResource::getUrl())
                    ->icon('heroicon-o-currency-dollar'),
                MenuItem::make()
                    ->label('Subscriptions')
                    ->url(fn (): string => SubscriptionResource::getUrl())
                    ->icon('heroicon-o-fire'),            
                MenuItem::make()
                    ->label(__('Workspace Settings'))
                    ->visible(
                        function () {
                            $tenantPermissionManager = app(TenantPermissionManager::class);

                            return $tenantPermissionManager->tenantUserHasPermissionTo(
                                Filament::getTenant(),
                                auth()->user(),
                                TenancyPermissionConstants::PERMISSION_UPDATE_TENANT_SETTINGS
                            );
                        }
                    )
                    ->icon('heroicon-s-cog-8-tooth')
                    ->url(fn () => TenantSettings::getUrl()),
            ])
            ->discoverResources(in: app_path('Filament/Dashboard/Resources'), for: 'App\\Filament\\Dashboard\\Resources')
            ->discoverPages(in: app_path('Filament/Dashboard/Pages'), for: 'App\\Filament\\Dashboard\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->colors([
                'primary' => Color::Blue,// '#1654D1',
                'danger' => Color::Rose,
                // 'gray' => Color::Slate, // Background
                // 'info' => Color::Blue,
                // 'success' => Color::Green,
                // 'warning' => Color::Red,
            ])
            ->brandLogoHeight('3rem')
            // ->brandLogo(asset('images/logo-dark.svg'))
            ->brandLogo(asset('images/logo-light.png'))
            ->darkModeBrandLogo(asset('images/logo-light.png'))
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->discoverWidgets(in: app_path('Filament/Dashboard/Widgets'), for: 'App\\Filament\\Dashboard\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->renderHook('panels::head.start', function () {
                return view('components.layouts.partials.analytics');
            })
            ->renderHook('panels::user-menu.before', function () {
                return view('filament/menus/top-right-menu');
            })
            // ->renderHook('panels::user-menu.before', function () {
            //     return Illuminate\Support\Facades\Blade::render('@livewire(\'database-notifications\')');
            // })
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Team')
                    ->icon('heroicon-s-users'),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->plugins([
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                        hasAvatars: false, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                    ->myProfileComponents([
                        \App\Livewire\AddressForm::class,
                    ]),
            ])
            ->tenantMenu()
            ->tenant(Tenant::class, 'uuid');
    }
}
