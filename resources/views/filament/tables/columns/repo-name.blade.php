@php
    $record = $getRecord();

    $providerIcon = match ($record->provider) {
        'bitbucket' => 'icon-bitbucket',
        'github' => 'icon-github',
        default => 'icon-git',
    };

    $providerColor = match ($record->provider) {
        'bitbucket' => 'text-blue-500',
        'github' => 'text-gray-700 dark:text-gray-300',
        default => 'text-orange-500',
    };

    $typeLabel = match ($record->type) {
        'plugin' => 'Plugin',
        'theme' => 'Theme',
        default => 'Other',
    };

    $typeColor = match ($record->type) {
        'plugin' => 'info',
        'theme' => 'success',
        default => 'gray',
    };

    // Build source URL from remote
    $sourceUrl = match ($record->provider) {
        'github' => str_replace(['git@github.com:', '.git'], ['https://github.com/', ''], $record->remote),
        'bitbucket' => str_replace(['git@bitbucket.org:', '.git'], ['https://bitbucket.org/', ''], $record->remote),
        default => null,
    };
@endphp

<div class="flex items-start gap-x-3 px-3 py-3">
    {{-- Provider icon --}}
    <div class="shrink-0 mt-0.5">
        <x-filament::icon
            :icon="$providerIcon"
            class="h-5 w-5 {{ $providerColor }}"
        />
    </div>

    {{-- Name + remote + type badge --}}
    <div class="flex flex-col gap-y-1 min-w-0">
        <div class="flex items-center gap-x-2">
            <span class="text-sm font-medium text-gray-950 dark:text-white truncate">
                {{ $record->name ?? 'Unnamed' }}
            </span>
            <x-filament::badge size="sm" :color="$typeColor">
                {{ $typeLabel }}
            </x-filament::badge>
        </div>

        <div class="flex items-center gap-x-1 min-w-0">
            @if ($sourceUrl)
                <a
                    href="{{ $sourceUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-x-1 text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:underline truncate"
                >
                    <x-filament::icon
                        icon="heroicon-m-arrow-top-right-on-square"
                        class="h-3 w-3 shrink-0"
                    />
                    <span class="truncate">{{ $record->remote }}</span>
                </a>
            @else
                <span class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $record->remote }}</span>
            @endif
        </div>
    </div>
</div>
