<div class="mr-2">
@if ( $unreadNotificationsCount == 0 )
<x-filament::icon-button
    alias="panels::topbar.open-database-notifications-button"
    icon="heroicon-o-bell"
    label="Notifications"
    color="gray"
    size="xl"
>
</x-filament::icon-button>
@else
<x-filament::icon-button
    alias="panels::topbar.open-database-notifications-button"
    icon="heroicon-o-bell"
    label="Notifications"
    size="xl"
>
    <x-slot name="badge">
        {{ $unreadNotificationsCount }}
    </x-slot>
</x-filament::icon-button>
@endif
</div>
