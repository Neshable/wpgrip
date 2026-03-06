<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.checkout_enabled', true)) {
            return redirect()->route('pricing');
        }

        return $next($request);
    }
}
