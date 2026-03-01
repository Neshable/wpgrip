<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;

use Filament\View\PanelsRenderHook;


class FilamentCustomHooksComponents
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::TENANT_MENU_BEFORE,
            fn (): View => view('filament/components/tenant-role'),
        );

        // Plan widget — pinned to the very bottom of the sidebar
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn (): View => view('filament/components/current-plan-info'),
        );

        // Global notificaiton for plan, subcriptions
        FilamentView::registerRenderHook(
            PanelsRenderHook::CONTENT_START,
            // PanelsRenderHook::SIDEBAR_NAV_END,
            fn (): View => view('filament/components/subscription-info'),
        );
        

        // Top right menu.
        // FilamentView::registerRenderHook(
        //     'panels::user-menu.before',
        //     fn (): View => view('filament/menus/top-right-menu'),
        // );
        
        // // Notifications bell
        // FilamentView::registerRenderHook(
        //     'panels::user-menu.before',
        //     fn (): string => Blade::render('@livewire(\'database-notifications\')'),
        // );

        // // Register login hooks.
        // FilamentView::registerRenderHook(
        //     'panels::auth.login.form.before',
        //     fn (): View => view('filament/components/before-login', ['requestUri' => $request->getPathInfo() ] )
        // );
        
        
        // Show the navigation menu for single site view.
        // FilamentView::registerRenderHook(
        //     'panels::page.start',
        //     fn (): View => view('filament/menus/internal-site-menu', ['requestUri' => $request->getPathInfo() ] ),
        //     scopes: [
        //         \App\Filament\Resources\SiteResource\Pages\ViewSite::class,
        //         \App\Filament\Resources\SiteResource\Pages\Monitoring::class,
        //         \App\Filament\Resources\SiteResource\Pages\ShowBackups::class,
        //         \App\Filament\Resources\SiteResource\Pages\Plugins::class,
        //         \App\Filament\Resources\SiteResource\Pages\Tests::class,
        //         \App\Filament\Resources\SiteResource\Pages\Git::class,
        //         \App\Filament\Resources\SiteResource\Pages\Tools::class,
        //         \App\Filament\Resources\SiteResource\Pages\Staging::class,
        //         \App\Filament\Resources\BackupResource\Pages\ListBackups::class,
        //         \App\Filament\Resources\BackupResource\Pages\ManageBackups::class,
        //     ]
            
        // );

     
        
        // At the bottom of the sidebar.
        // FilamentView::registerRenderHook(
        //     'panels::content.start',
        //     fn (): View => view('filament/menus/internal-site-menu-vertical')
        // );

 
        return $next($request);
    }
}
