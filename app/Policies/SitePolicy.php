<?php

namespace App\Policies;

use App\Constants\TenancyPermissionConstants;
use App\Models\Site;
use App\Models\User;
use App\Services\TenantPermissionManager;
use App\Services\SubscriptionManager;
use Filament\Facades\Filament;

class SitePolicy
{
    public function __construct(
        private TenantPermissionManager $tenantPermissionManager
    ) {

    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $tenant = Filament::getTenant();

        // Tenant was created by the current user.
        if ($tenant->created_by == $user->id) {
            // No active plan?
            return $user->isSubscribed();
        }

        // If that's external workspace, then grant access.
        if ( $this->tenantPermissionManager->tenantUserHasPermissionTo(
            $tenant,
            $user,
            TenancyPermissionConstants::PERMISSION_VIEW_SITES,
        ) ) {
            return true;
        }
        
        
       
        return $user->hasPermissionTo('view sites');

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Site $site): bool
    {
        // If that's external workspace, then grant access.
        if ( $this->tenantPermissionManager->tenantUserHasPermissionTo(
            $site->tenant,
            $user,
            TenancyPermissionConstants::PERMISSION_VIEW_SITES,
        ) ) {
            return true;
        }
        // No active plan?
        if (  !$user->isSubscribed() ) {
            return false;
        }
       
        return $user->hasPermissionTo('view sites');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user ): bool
    {
        $tenant = Filament::getTenant();

        if ($tenant->created_by == $user->id) {
            // Tenant was created by the current user.
            // No active plan?
            return $user->isSubscribed();
        }

        if ( $this->tenantPermissionManager->tenantUserHasPermissionTo(
            Filament::getTenant(),
            $user,
            TenancyPermissionConstants::PERMISSION_CREATE_SITES,
        ) ) {
            return true;
        }
        
        // No active plan?
        if ( !$user->isSubscribed() ) {
            return false;
        }

        return $user->hasPermissionTo('create sites');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Site $site): bool
    {
        return $user->hasPermissionTo('update sites') || $this->tenantPermissionManager->tenantUserHasPermissionTo(
            $site->tenant,
            $user,
            TenancyPermissionConstants::PERMISSION_UPDATE_SITES,
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Site $site): bool
    {
        return $user->hasPermissionTo('delete sites') || $this->tenantPermissionManager->tenantUserHasPermissionTo(
            $site->tenant,
            $user,
            TenancyPermissionConstants::PERMISSION_DELETE_SITES,
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Site $site): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Site $site): bool
    {
        return false;
    }
}
