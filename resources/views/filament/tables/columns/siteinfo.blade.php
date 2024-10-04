<div class="flex max-w-full gap-x-1 overflow-x-auto ">
    @php
        $monitor = $getRecord()->get_main_monitor() ?? null;
    @endphp

     <x-filament::icon
    x-tooltip="{
        content: '{{ $getRecord()->getConnectionStatus() ? 'Connection is established.' : 'Issue with SSH connection.' }}',
        theme: $store.theme,
    }"
    icon="{{ $getRecord()->getConnectionStatus() ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle' }}"
    class="h-5 w-5 {{ $getRecord()->getConnectionStatus() ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400' }}"
    />

    
    <x-filament::icon
        x-tooltip="{
            content: '{{ $monitor ? ($monitor->certificate_status == 'valid' ? 'SSL is valid.' : 'No SSL found.') : 'SSL monitor not set.'}}',
            theme: $store.theme,
        }"
        icon="heroicon-m-shield-check"
        class="h-5 w-5 {{ $monitor ? ($monitor->certificate_status == 'valid' ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400') : 'text-gray-500 dark:text-gray-400'}}"
    />

    <x-filament::icon
        x-tooltip="{
            content: '{{ $monitor ? ($monitor->uptime_status == 'up' ? 'Website is up and running.' : 'Website is down.') : 'Uptime monitor'}}',
            theme: $store.theme,
        }"
        icon="heroicon-m-arrow-trending-up"
        class="h-5 w-5 {{ $monitor ? ($monitor->uptime_status == 'up' ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400') : 'text-gray-500 dark:text-gray-400'}}"
    />
    {{--
    <x-filament::icon
        x-tooltip="{
            content: '{{ $getRecord()->backups() ? 'Backup enabled' : 'Backup Disabled'}}',
            theme: $store.theme,
        }"
        icon="icon-backups"
        class="h-5 w-5 {{ $getRecord()->backups() ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400'}}"
    /> --}}

    {{-- <x-filament::icon
        x-tooltip="{
            content: '{{ $getRecord()->backups() ? 'Backup enabled' : 'Backup Disabled'}}',
            theme: $store.theme,
        }"
        icon="icon-git"
        class="h-5 w-5 {{ $getRecord()->backups() ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400'}}"
    /> --}}

    

</div>
