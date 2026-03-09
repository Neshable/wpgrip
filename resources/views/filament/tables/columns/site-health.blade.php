@php
    $record = $getRecord();
    $monitor = $record->get_main_monitor() ?? null;
@endphp

<div class="flex items-center gap-x-2 px-3 py-3">
    <x-filament::icon
        x-tooltip="{
            content: '{{ $record->getConnectionStatus() ? 'SSH connection established.' : 'Issue with SSH connection.' }}',
            theme: $store.theme,
        }"
        icon="{{ $record->getConnectionStatus() ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle' }}"
        class="h-5 w-5 shrink-0 {{ $record->getConnectionStatus() ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400' }}"
    />

    <x-filament::icon
        x-tooltip="{
            content: '{{ $monitor ? ($monitor->certificate_status == 'valid' ? 'SSL certificate is valid.' : 'SSL certificate issue.') : 'SSL monitor not set.' }}',
            theme: $store.theme,
        }"
        icon="heroicon-m-shield-check"
        class="h-5 w-5 shrink-0 {{ $monitor ? ($monitor->certificate_status == 'valid' ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400') : 'text-gray-400 dark:text-gray-500' }}"
    />

    <x-filament::icon
        x-tooltip="{
            content: '{{ $monitor ? ($monitor->uptime_status == 'up' ? 'Website is up and running.' : 'Website is down.') : 'Uptime monitor not set.' }}',
            theme: $store.theme,
        }"
        icon="{{ $monitor ? ($monitor->uptime_status == 'up' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down') : 'heroicon-m-arrow-trending-up' }}"
        class="h-5 w-5 shrink-0 {{ $monitor ? ($monitor->uptime_status == 'up' ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400') : 'text-gray-400 dark:text-gray-500' }}"
    />
</div>
