<?php

namespace App\Services;

use App\Models\User;

class UserDashboardManager
{
    public function getUserDashboardUrl(User $user)
    {
        // If the user has a free plan, or doesn't have any tenant yet
        // if ( !$user->isSubscribed() ) {
        //     return route('home-no-plan');
        // }

        $tenant = $user->tenants()->orderByPivot('is_default', 'desc')->first();

        if ($tenant !== null) {
            return route('filament.dashboard.pages.dashboard', ['tenant' => $tenant]);
        }

        return route('get-started');

        // return route('home');
    }

    // public function getUserAlternativeDashboard(User $user)
    // {
    //     // If the user has a free plan, or doesn't have any tenant yet
    //     // if ( !$user->isSubscribed() ) {
    //     //     return route('home-no-plan');
    //     // }

    //     $tenant = $user->tenants()->orderByPivot('is_default', 'desc')->first();

    //     if ($tenant !== null) {
    //         return route('filament.dashboard.pages.dashboard', ['tenant' => $tenant]);
    //     }

    //    // return route('get-started');

    //     // return route('home');
    // }
}
