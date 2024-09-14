<div class="flex max-w-full gap-x-1 overflow-x-auto ">
    @php
        $monitor = $getRecord()->get_main_monitor() ?? null;
    @endphp

     <x-filament::icon
    x-tooltip="{
        content: '{{ $getRecord()->getConnectionStatus() ? 'Connection is established.' : 'Issue with connection. Please check.' }}',
        theme: $store.theme,
    }"
    icon="heroicon-o-check-circle"
    class="h-5 w-5 {{ $getRecord()->getConnectionStatus() ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}"
    />

    
    <x-filament::icon
        x-tooltip="{
            content: '{{ $monitor ? ($monitor->certificate_status == 'valid' ? 'SSL is valid.' : 'No SSL found.') : 'SSL monitor not set.'}}',
            theme: $store.theme,
        }"
        icon="heroicon-m-shield-check"
        class="h-5 w-5 {{ $monitor ? ($monitor->certificate_status == 'valid' ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400') : 'text-gray-500 dark:text-gray-400'}}"
    />

    <x-filament::icon
        x-tooltip="{
            content: '{{ $monitor ? ($monitor->uptime_status == 'up' ? 'Website is up and running.' : 'Website is down.') : 'Uptime monitor'}}',
            theme: $store.theme,
        }"
        icon="heroicon-m-arrow-trending-up"
        class="h-5 w-5 {{ $monitor ? ($monitor->uptime_status == 'up' ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400') : 'text-gray-500 dark:text-gray-400'}}"
    />
    {{--
    <x-filament::icon
        x-tooltip="{
            content: '{{ $getRecord()->backups() ? 'Backup enabled' : 'Backup Disabled'}}',
            theme: $store.theme,
        }"
        icon="icon-backups"
        class="h-5 w-5 {{ $getRecord()->backups() ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400'}}"
    /> --}}

    {{-- <x-filament::icon
        x-tooltip="{
            content: '{{ $getRecord()->backups() ? 'Backup enabled' : 'Backup Disabled'}}',
            theme: $store.theme,
        }"
        icon="icon-git"
        class="h-5 w-5 {{ $getRecord()->backups() ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400'}}"
    /> --}}

    

</div>
