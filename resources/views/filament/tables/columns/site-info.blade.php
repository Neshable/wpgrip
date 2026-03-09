@php
    $record = $getRecord();
@endphp

<div class="flex flex-col gap-y-1 px-3 py-3">
    {{-- Row 1: Favicon + Name + Badge --}}
    <div class="flex items-center gap-x-2">
        <img
            src="https://s2.googleusercontent.com/s2/favicons?domain={{ $record->url }}"
            alt=""
            class="h-4 w-4 shrink-0 rounded-full"
            loading="lazy"
        />
        <span class="text-sm font-medium text-gray-950 dark:text-white">
            {{ $record->name ?? 'n/a' }}
        </span>
        @if ($record->is_staging)
            <x-filament::badge size="sm" color="gray">
                Staging
            </x-filament::badge>
        @else
            <x-filament::badge size="sm" color="info">
                Production
            </x-filament::badge>
        @endif
    </div>

    {{-- Row 2: URL with external link --}}
    <a
        href="{{ $record->url ?? '#' }}"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center gap-x-1 text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:underline"
    >
        <x-filament::icon
            icon="heroicon-m-arrow-top-right-on-square"
            class="h-3.5 w-3.5 shrink-0"
        />
        <span class="truncate">{{ $record->url ?? 'n/a' }}</span>
    </a>

    {{-- Row 3: WP version + PHP version + WP-CLI version --}}
    <div class="flex items-center gap-x-3">
        <span class="inline-flex items-center gap-x-1 text-xs text-gray-400 dark:text-gray-500"
              title="WordPress version">
            <x-filament::icon
                icon="icon-wordpress"
                class="h-3.5 w-3.5 shrink-0"
            />
            {{ $record->wp_ver ?? 'n/a' }}
        </span>
        <span class="inline-flex items-center gap-x-1 text-xs text-gray-400 dark:text-gray-500"
              title="PHP version">
            <x-filament::icon
                icon="icon-php"
                class="h-3.5 w-3.5 shrink-0"
            />
            {{ $record->php_ver ?? 'n/a' }}
        </span>
        <span class="inline-flex items-center gap-x-1 text-xs text-gray-400 dark:text-gray-500"
              title="WP-CLI version">
            <x-filament::icon
                icon="heroicon-m-command-line"
                class="h-3.5 w-3.5 shrink-0"
            />
            {{ $record->cli_ver ?? 'n/a' }}
        </span>
    </div>
</div>
