<?php

namespace App\Providers\Filament;

use App\Constants\AnnouncementPlacement;
use App\Constants\TenancyPermissionConstants;
use App\Filament\Dashboard\Pages\TenantSettings;
use App\Filament\Dashboard\Pages\TwoFactorAuth\TwoFactorAuth;
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
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use App\Http\Middleware\FilamentCustomHooksComponents;
use App\Filament\Dashboard\Widgets as DashboardWidgets;

use App\Filament\Dashboard\Pages\Team;
use App\Filament\Dashboard\Resources\OrderResource;
use App\Filament\Dashboard\Resources\TransactionResource;
use App\Filament\Dashboard\Resources\SubscriptionResource;
// use App\Filament\Dashboard\Resources\InvitationResource;


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
            ->darkMode(true)
            ->unsavedChangesAlerts()
            ->maxContentWidth('screen-4xl')
            // ->sidebarFullyCollapsibleOnDesktop()
            ->userMenuItems([
                MenuItem::make()
                    ->label(__('Admin Panel'))
                    ->visible(
                        function () {
                            /** @var \App\Models\User $user */
                            $user = auth()->user();
                            return $user && $user->isAdmin();
                        }
                    )
                    ->url(fn () => route('filament.admin.pages.dashboard'))
                    ->icon('heroicon-s-cog-8-tooth'),
                MenuItem::make()
                    ->label('Invitations')
                    ->url(fn (): string => '/invitations' )
                    ->icon('heroicon-o-envelope-open'),
                MenuItem::make()
                    ->label('Orders')
                    ->url(fn (): string => OrderResource::getUrl())
                    ->icon('heroicon-o-rectangle-stack'),
                MenuItem::make()
                    ->label('Payment History')
                    ->icon('heroicon-o-credit-card')
                    ->visible(
                        function () {
                            $tenantPermissionManager = app(TenantPermissionManager::class);

                            $tenant = Filament::getTenant();
                            /** @var \App\Models\User $user */
                            $user = auth()->user();

                            // Tenant was created by the current user.
                            if ($tenant->created_by == $user->id && !$user->isSubscribed() ) {
                                return false;
                            }

                            return $tenantPermissionManager->tenantUserHasPermissionTo(
                                $tenant,
                                $user,
                                TenancyPermissionConstants::PERMISSION_UPDATE_SUBSCRIPTIONS
                            );
                        }
                    )
                    ->url(fn (): string => TransactionResource::getUrl()),
                MenuItem::make()
                    ->label('Subscription')
                    ->icon('heroicon-o-rectangle-stack')
                    ->visible(
                        function () {
                            $tenantPermissionManager = app(TenantPermissionManager::class);

                            $tenant = Filament::getTenant();
                            /** @var \App\Models\User $user */
                            $user = auth()->user();

                            // Tenant was created by the current user.
                            if ($tenant->created_by == $user->id && !$user->isSubscribed() ) {
                                return false;
                            }

                            return $tenantPermissionManager->tenantUserHasPermissionTo(
                                $tenant,
                                $user,
                                TenancyPermissionConstants::PERMISSION_UPDATE_SUBSCRIPTIONS
                            );
                        }
                    )
                    ->icon('heroicon-s-cog-8-tooth')
                    ->url(fn () => SubscriptionResource::getUrl()),
                MenuItem::make()
                    ->label(__('2-Factor Authentication'))
                    ->visible(
                        fn () => config('app.two_factor_auth_enabled')
                    )
                    ->url(fn () => TwoFactorAuth::getUrl())
                    ->icon('heroicon-s-cog-8-tooth'),
            ])
            ->discoverResources(in: app_path('Filament/Dashboard/Resources'), for: 'App\\Filament\\Dashboard\\Resources')
            ->discoverPages(in: app_path('Filament/Dashboard/Pages'), for: 'App\\Filament\\Dashboard\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->favicon(asset('images/favicon.png'))
            ->breadcrumbs(false)
            ->databaseNotifications()
            ->colors([
                'primary' => [  // brand blue — matches main site
                    50  => '235, 240, 255',
                    100 => '214, 224, 255',
                    200 => '173, 196, 255',
                    300 => '122, 161, 255',
                    400 => '82,  124, 246',
                    500 => '63,  99,  230',  // #3f63e6
                    600 => '50,  79,  204',
                    700 => '40,  63,  170',
                    800 => '31,  50,  138',
                    900 => '24,  39,  108',
                    950 => '14,  23,  71',
                ],
                'danger'  => Color::Rose,
                'gray'    => [
                    // Light end (used for light-mode surfaces) — keep neutral
                    50  => '249, 250, 251',
                    100 => '243, 244, 246',
                    200 => '229, 231, 235',
                    300 => '209, 213, 219',
                    400 => '156, 163, 175',
                    500 => '107, 114, 128',
                    600 => '75,  85,  99',
                    // Dark end — cool near-blacks matching neutral-950 palette
                    700 => '31,  32,  35',
                    800 => '20,  20,  20',
                    900 => '17,  17,  17',
                    950 => '10,  10,  10',
                ],
                'success' => Color::Green,
            ])
            ->brandLogoHeight('3rem')
            // ->brandLogo(asset('images/logo-dark.svg'))
            ->brandLogo(asset('images/logo-light.png'))
            ->darkModeBrandLogo(asset('images/logo-light.png'))
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->discoverWidgets(in: app_path('Filament/Dashboard/Widgets'), for: 'App\\Filament\\Dashboard\\Widgets')
            ->widgets([
                DashboardWidgets\SitesOverview::class,

                DashboardWidgets\WPVersionChart::class,
                DashboardWidgets\PHPVersionChart::class,
                DashboardWidgets\PluginChart::class,
                // Widgets\AccountWidget::class,
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
                 // Here we can hook all our custom hooks.
                 FilamentCustomHooksComponents::class,
            ])
            ->renderHook('panels::head.start', function () {
                return view('components.layouts.partials.analytics');
            })
            ->renderHook('panels::user-menu.before', function () {
                return view('filament/menus/top-right-menu');
            })
            ->renderHook('panels::user-menu.before', function () {
                return view('filament/notifications/database-notifications-trigger');
            })
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Team')
                    ->extraSidebarAttributes(['class' => 'featured-sidebar-group'])
                    ->icon('heroicon-s-users'),
                NavigationGroup::make()
                    ->label('Billing')
                    ->extraSidebarAttributes(['class' => 'featured-sidebar-group'])
                    ->collapsed()
                    ->icon('heroicon-o-rectangle-stack'),
            ])
            ->navigationItems([              
                
                // Group billing
               
               
            ])
            ->renderHook(PanelsRenderHook::BODY_START,
                fn (): string => Blade::render("@livewire('announcement.view', ['placement' => '".AnnouncementPlacement::USER_DASHBOARD->value."'])")
            )
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
            ->tenantMenuItems([
                MenuItem::make('Space Settings')
                    ->label('Workspace Settings')
                    ->icon('heroicon-s-cog-8-tooth')
                    ->visible(
                        function () {
                            $tenantPermissionManager = app(TenantPermissionManager::class);
                            /** @var \App\Models\User $user */
                            $user = auth()->user();

                            return $tenantPermissionManager->tenantUserHasPermissionTo(
                                Filament::getTenant(),
                                $user,
                                TenancyPermissionConstants::PERMISSION_UPDATE_TENANT_SETTINGS
                            );
                        }
                    )
                    ->url(fn () => TenantSettings::getUrl())
                    ->sort(3),
                MenuItem::make('Team Members')
                    ->label('Team Members')
                    ->icon('heroicon-s-users')
                    ->visible(
                        function () {
                            $tenantPermissionManager = app(TenantPermissionManager::class);
                            /** @var \App\Models\User $user */
                            $user = auth()->user();

                            return $tenantPermissionManager->tenantUserHasPermissionTo(
                                Filament::getTenant(),
                                $user,
                                TenancyPermissionConstants::PERMISSION_UPDATE_TENANT_SETTINGS
                            );
                        }
                    )
                    ->url(fn (): string => Team::getUrl())
                    ->sort(3),
                // MenuItem::make('Invite Members')
                //     ->label('Invite Members')
                //     ->visible(
                //         function () {
                //             $tenantPermissionManager = app(TenantPermissionManager::class);

                //             return $tenantPermissionManager->tenantUserHasPermissionTo(
                //                 Filament::getTenant(),
                //                 auth()->user(),
                //                 TenancyPermissionConstants::PERMISSION_UPDATE_TENANT_SETTINGS
                //             );
                //         }
                //     )
                //     ->url(fn (): string => InvitationResource::getUrl() )
                //     ->sort(3),

                
            ])
            ->tenantMenu()
            ->tenant(Tenant::class, 'uuid');
    }
}
