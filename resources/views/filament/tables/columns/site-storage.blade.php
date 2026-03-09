@php
    $record = $getRecord();

    $dirSize = $record->dir_size;
    $filesValue = $record->getFormatedDBSize();
    $dbValue = $record->getDBSize();
    $dbSizeRaw = $record->sitemeta?->db_size;

    // Files color logic
    if (!$dirSize) {
        $filesColor = 'text-gray-400 dark:text-gray-500';
        $filesIcon = null;
    } elseif ($dirSize >= 10240) {
        $filesColor = 'text-amber-500 dark:text-amber-400';
        $filesIcon = 'heroicon-m-exclamation-triangle';
    } else {
        $filesColor = 'text-emerald-600 dark:text-emerald-400';
        $filesIcon = null;
    }

    // DB color logic
    if (!$dbSizeRaw) {
        $dbColor = 'text-gray-400 dark:text-gray-500';
        $dbIcon = null;
    } elseif ($dbSizeRaw >= 500) {
        $dbColor = 'text-amber-500 dark:text-amber-400';
        $dbIcon = 'heroicon-m-exclamation-triangle';
    } else {
        $dbColor = 'text-emerald-600 dark:text-emerald-400';
        $dbIcon = null;
    }
@endphp

<div class="flex flex-col gap-y-1 px-3 py-3">
    {{-- Files row --}}
    <div class="flex items-center gap-x-1.5">
        <span class="text-xs text-gray-400 dark:text-gray-500 w-8 shrink-0">Files</span>
        @if ($filesValue)
            @if ($filesIcon)
                <x-filament::icon
                    icon="{{ $filesIcon }}"
                    class="h-3.5 w-3.5 shrink-0 {{ $filesColor }}"
                />
            @endif
            <span class="text-xs font-medium tabular-nums {{ $filesColor }}">
                {{ $filesValue }}
            </span>
        @else
            <span class="text-xs text-gray-300 dark:text-gray-600">&mdash;</span>
        @endif
    </div>

    {{-- DB row --}}
    <div class="flex items-center gap-x-1.5">
        <span class="text-xs text-gray-400 dark:text-gray-500 w-8 shrink-0">DB</span>
        @if ($dbValue)
            @if ($dbIcon)
                <x-filament::icon
                    icon="{{ $dbIcon }}"
                    class="h-3.5 w-3.5 shrink-0 {{ $dbColor }}"
                />
            @endif
            <span class="text-xs font-medium tabular-nums {{ $dbColor }}">
                {{ $dbValue }}
            </span>
        @else
            <span class="text-xs text-gray-300 dark:text-gray-600">&mdash;</span>
        @endif
    </div>
</div>
