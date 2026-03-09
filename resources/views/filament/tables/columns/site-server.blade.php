@php
    $record = $getRecord();
    $server = $record->server;
@endphp

<div class="flex flex-col gap-y-0.5 px-3 py-3">
    {{-- Server name --}}
    <span class="text-sm font-medium text-gray-950 dark:text-white">
        {{ $server?->name ?? 'n/a' }}
    </span>

    {{-- IP address --}}
    @if ($server?->ip)
        <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
            {{ $server->ip }}
        </span>
    @endif

    {{-- Site path --}}
    @if ($record->dir_path)
        <span
            class="text-xs text-gray-400 dark:text-gray-500 font-mono truncate max-w-[180px]"
            title="{{ $record->dir_path }}"
        >
            {{ $record->dir_path }}
        </span>
    @endif
</div>
