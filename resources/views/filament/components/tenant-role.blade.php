@php
    $user = Filament\Facades\Filament::auth()->user();
    $tenant = Filament\Facades\Filament::getTenant();
    
    $roleName = 'Member';
    $roleColor = 'gray'; // Default color

    if ($tenant && $user) {
        $roles = app(\App\Services\TenantPermissionManager::class)->getTenantUserRoles($tenant, $user);
        if (!empty($roles)) {
            $roleName = \Illuminate\Support\Str::of($roles[0])
                ->replace(\App\Constants\TenancyPermissionConstants::TENANCY_ROLE_PREFIX, '')
                ->replace('-', ' ')
                ->title();
                
            // Optional: Customize colors based on role
            $roleColor = match((string)$roleName) {
                'Admin' => 'primary',
                'Owner' => 'primary',
                default => 'gray',
            };
        } elseif ($tenant->created_by == $user->id) {
             $roleName = 'Owner';
             $roleColor = 'primary';
        }
    }
@endphp

<div class="px-2 mb-2 mt-2">
    <div class="flex items-center justify-between p-2.5 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200/50 dark:border-white/10">
        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
            Workspace
        </span>
        <x-filament::badge :color="$roleColor" size="xs">
            {{ $roleName }}
        </x-filament::badge>
    </div>
</div>