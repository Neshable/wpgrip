@php
    $isUpdate   = $highlight === 'update';
    $isInactive = $highlight === 'inactive';
    $isActive   = $theme['status'] === 'active';
    $hasUpdate  = !empty($theme['update_version']);
@endphp

<div
    x-data="{ open: false }"
    class="relative flex items-center gap-4 px-4 py-3 group
        {{ !$last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}
        {{ $isUpdate   ? 'bg-amber-50/60 dark:bg-amber-900/10' : '' }}
        {{ $isInactive ? 'bg-gray-50 dark:bg-gray-800/30' : 'bg-white dark:bg-gray-900/20' }}
        hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
>
    <div class="flex-shrink-0 w-2 h-2 rounded-full mt-0.5
        {{ $isActive ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                {{ $theme['title'] }}
            </span>
            @if ($theme['is_vulnerable'])
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                    <x-filament::icon icon="heroicon-m-shield-exclamation" class="w-3 h-3" />
                    Vulnerable
                </span>
            @endif
        </div>
        @if (!empty($theme['description']))
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate max-w-lg">
            {{ $theme['description'] }}
        </p>
        @endif
    </div>

    <div class="flex-shrink-0 flex items-center gap-2">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium
            {{ $isInactive
                ? 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'
                : 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' }}">
            {{ $theme['version'] ?: '—' }}
        </span>

        @if ($hasUpdate)
            <x-filament::icon icon="heroicon-m-arrow-right" class="w-3.5 h-3.5 text-amber-500" />
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-mono font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                {{ $theme['update_version'] }}
            </span>
        @endif
    </div>

    <div class="flex-shrink-0 relative" x-data="{ open: false }">
        @if ($isActive)
            {{-- Active theme: show badge instead of actions (can't deactivate the active theme) --}}
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                <x-filament::icon icon="heroicon-m-check-circle" class="w-3.5 h-3.5" />
                Active theme
            </span>
        @else
            <button
                @click="open = !open"
                class="p-1 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
                <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="w-4 h-4" />
            </button>

            <div
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 top-8 z-50 w-44 rounded-lg shadow-lg
                       bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-1"
                style="display:none"
            >
                <button
                    wire:click="activateTheme({{ $theme['id'] }})"
                    @click="open = false"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors"
                >
                    <x-filament::icon icon="heroicon-m-play-circle" class="w-4 h-4" />
                    Activate
                </button>

                <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>

                <button
                    wire:click="deleteTheme({{ $theme['id'] }})"
                    @click="open = false"
                    wire:confirm="Remove this theme from the list?"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                >
                    <x-filament::icon icon="heroicon-m-trash" class="w-4 h-4" />
                    Remove
                </button>
            </div>
        @endif
    </div>

    <div wire:loading wire:target="activateTheme({{ $theme['id'] }}),deleteTheme({{ $theme['id'] }})"
         class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 flex items-center justify-center rounded">
        <x-filament::loading-indicator class="w-5 h-5 text-primary-500" />
    </div>
</div>
