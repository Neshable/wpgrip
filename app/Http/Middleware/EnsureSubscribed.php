<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Paths that should bypass the subscription check (e.g. checkout, subscription management).
     */
    protected array $except = [
        'checkout/*',
        'pricing',
        'plan/*',
        'subscription/*',
        'already-subscribed',
        'dashboard/*/subscriptions*',
        'dashboard/*/orders*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Let excluded paths through (checkout, pricing, subscription management)
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        $user = $request->user();

        // No authenticated user — let other middleware handle that
        if (! $user) {
            return $next($request);
        }

        // Admins always pass
        if ($user->isAdmin()) {
            return $next($request);
        }

        $tenant = Filament::getTenant();

        // No tenant resolved yet (e.g. tenant picker screen) — let through
        if (! $tenant) {
            return $next($request);
        }

        // Check whether the user has an active subscription for this tenant
        if ($user->isSubscribed(null, $tenant)) {
            return $next($request);
        }

        // No active subscription — redirect to pricing page
        return redirect()->route('pricing');
    }
}
