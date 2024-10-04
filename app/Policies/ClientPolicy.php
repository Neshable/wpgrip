<?php

namespace App\Policies;

use App\Constants\TenancyPermissionConstants;
use App\Models\Client;
use App\Models\User;
use App\Services\TenantPermissionManager;
use Filament\Facades\Filament;

class ClientPolicy
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
        return $user->hasPermissionTo('view clients') || $this->tenantPermissionManager->tenantUserHasPermissionTo(
            Filament::getTenant(),
            $user,
            TenancyPermissionConstants::PERMISSION_VIEW_CLIENTS,
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('view clients') || $this->tenantPermissionManager->tenantUserHasPermissionTo(
            $client->tenant,
            $user,
            TenancyPermissionConstants::PERMISSION_VIEW_CLIENTS,
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('update clients') || $this->tenantPermissionManager->tenantUserHasPermissionTo(
            $client->tenant,
            $user,
            TenancyPermissionConstants::PERMISSION_UPDATE_CLIENTS,
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('delete clients') || $this->tenantPermissionManager->tenantUserHasPermissionTo(
            $client->tenant,
            $user,
            TenancyPermissionConstants::PERMISSION_DELETE_CLIENTS,
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }
}
