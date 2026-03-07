<div>
    @php
        $all        = collect($themes);
        $withUpdate = $all->filter(fn($t) => !empty($t['update_version']));
        $active     = $all->filter(fn($t) => $t['status'] === 'active' && empty($t['update_version']));
        $inactive   = $all->filter(fn($t) => $t['status'] === 'inactive');
        $totalCount  = $all->count();
        $updateCount = $withUpdate->count();
        $activeCount = $all->filter(fn($t) => $t['status'] === 'active')->count();
        $inactiveCount = $inactive->count();
    @endphp

    {{-- ── HEADER SECTION ─────────────────────────────────────────────── --}}
    <x-filament::section>
        <x-slot name="heading">Themes</x-slot>

        <x-slot name="headerEnd">
            <button
                wire:click="syncThemes"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium
                       bg-primary-600 hover:bg-primary-700 text-white transition-colors
                       disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="syncThemes">
                    <x-filament::icon icon="heroicon-m-arrow-path" class="w-4 h-4" />
                </span>
                <span wire:loading wire:target="syncThemes">
                    <x-filament::loading-indicator class="w-4 h-4" />
                </span>
                Sync list
            </button>
        </x-slot>

        {{-- Stat pills --}}
        <div class="flex flex-wrap gap-3">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300">
                <x-filament::icon icon="heroicon-m-swatch" class="w-4 h-4 text-gray-400" />
                {{ $totalCount }} total
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-green-100 dark:bg-green-900/30 text-sm font-medium text-green-700 dark:text-green-300">
                <x-filament::icon icon="heroicon-m-check-circle" class="w-4 h-4" />
                {{ $activeCount }} active
            </div>
            @if ($inactiveCount)
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">
                <x-filament::icon icon="heroicon-m-pause-circle" class="w-4 h-4" />
                {{ $inactiveCount }} inactive
            </div>
            @endif
            @if ($updateCount)
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-sm font-medium text-amber-700 dark:text-amber-300">
                <x-filament::icon icon="heroicon-m-arrow-up-circle" class="w-4 h-4" />
                {{ $updateCount }} update{{ $updateCount > 1 ? 's' : '' }} available
            </div>
            @endif
        </div>
    </x-filament::section>

    {{-- ── UPDATES AVAILABLE ────────────────────────────────────────────── --}}
    @if ($withUpdate->isNotEmpty())
    <x-filament::section class="mt-4"
        icon="heroicon-o-arrow-up-circle"
        icon-color="warning"
    >
        <x-slot name="heading">Updates Available</x-slot>
        <x-slot name="description">{{ $updateCount }} theme{{ $updateCount > 1 ? 's have' : ' has' }} a newer version ready to install.</x-slot>

        <div class="overflow-hidden rounded-lg border border-amber-200 dark:border-amber-800/50">
            @foreach ($withUpdate as $i => $theme)
                @include('livewire.themes-panel-row', ['theme' => $theme, 'highlight' => 'update', 'last' => $loop->last])
            @endforeach
        </div>
    </x-filament::section>
    @endif

    {{-- ── ACTIVE THEMES ───────────────────────────────────────────────── --}}
    <x-filament::section class="mt-4"
        icon="heroicon-o-check-circle"
        icon-color="success"
    >
        <x-slot name="heading">Active</x-slot>
        <x-slot name="description">{{ $activeCount }} theme{{ $activeCount !== 1 ? 's' : '' }} currently active on your site.</x-slot>

        @if ($active->isNotEmpty())
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            @foreach ($active as $theme)
                @include('livewire.themes-panel-row', ['theme' => $theme, 'highlight' => 'active', 'last' => $loop->last])
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400 dark:text-gray-500 italic">All active themes have updates pending.</p>
        @endif
    </x-filament::section>

    {{-- ── INACTIVE THEMES ─────────────────────────────────────────────── --}}
    @if ($inactive->isNotEmpty())
    <x-filament::section class="mt-4"
        icon="heroicon-o-pause-circle"
        icon-color="gray"
    >
        <x-slot name="heading">Inactive</x-slot>
        <x-slot name="description">{{ $inactiveCount }} theme{{ $inactiveCount !== 1 ? 's are' : ' is' }} installed but not active.</x-slot>

        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 opacity-75">
            @foreach ($inactive as $theme)
                @include('livewire.themes-panel-row', ['theme' => $theme, 'highlight' => 'inactive', 'last' => $loop->last])
            @endforeach
        </div>
    </x-filament::section>
    @endif

    @if ($all->isEmpty())
    <x-filament::section class="mt-4">
        <div class="flex flex-col items-center py-10 text-gray-400 dark:text-gray-500">
            <x-filament::icon icon="heroicon-o-swatch" class="w-12 h-12 mb-3 opacity-40" />
            <p class="text-base font-medium">No themes found</p>
            <p class="text-sm mt-1">Click <strong>Sync list</strong> to pull themes from your site.</p>
        </div>
    </x-filament::section>
    @endif
</div>
