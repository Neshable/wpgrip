@extends('site/single/pagetemplate')

@section('content')

@php
    $site   = $this->getRecord();
    $meta   = $site->sitemeta;
    $server = $site->server;
    $client = $site->client;
    $mon    = $site->get_main_monitor();

    $pluginCount        = $site->plugins->count();
    $pluginUpdateCount  = $site->plugins->filter(fn($p) => $p->pivot->update_version)->count();
    $themeCount         = $site->themes->count();
    $themeUpdateCount   = $site->themes->filter(fn($t) => $t->pivot->update_version)->count();
    $totalUpdates       = $pluginUpdateCount + $themeUpdateCount;

    $certExpiry     = $mon?->certificate_expiration_date
                        ? \Carbon\Carbon::parse($mon->certificate_expiration_date)
                        : null;
    $certDaysLeft   = $certExpiry ? now()->diffInDays($certExpiry, false) : null;
    $certOk         = $certDaysLeft !== null && $certDaysLeft > 14;

    $domainExpiry   = $meta?->domain_expiry_date
                        ? \Carbon\Carbon::parse($meta->domain_expiry_date)
                        : null;
    $domainDaysLeft = $domainExpiry ? now()->diffInDays($domainExpiry, false) : null;

    $lastSync = $site->last_sync
        ? (\Carbon\Carbon::parse($site->last_sync)->diffForHumans())
        : 'Never';

    $uptimeUp   = $mon?->uptime_status === 'up';
    $dbTables   = json_decode($meta?->db_tables ?? '[]', true) ?? [];

    $tenant = Filament\Facades\Filament::getTenant();
@endphp

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     ROW 1 — STATUS STRIP
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

    {{-- Uptime --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4">
        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center
            {{ $uptimeUp ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
            <x-filament::icon
                icon="{{ $uptimeUp ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle' }}"
                class="h-6 w-6 {{ $uptimeUp ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}"
            />
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Uptime</p>
            <p class="text-sm font-semibold {{ $uptimeUp ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                {{ $uptimeUp ? 'Online' : 'Down' }}
            </p>
        </div>
    </div>

    {{-- SSL Certificate --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4">
        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center
            {{ $certOk ? 'bg-green-100 dark:bg-green-900/30' : 'bg-amber-100 dark:bg-amber-900/30' }}">
            <x-filament::icon
                icon="{{ $certOk ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open' }}"
                class="h-6 w-6 {{ $certOk ? 'text-green-600 dark:text-green-400' : 'text-amber-500' }}"
            />
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">SSL Cert</p>
            @if ($certExpiry)
                <p class="text-sm font-semibold {{ $certOk ? 'text-green-600 dark:text-green-400' : 'text-amber-500' }}">
                    {{ (int) $certDaysLeft }}d left
                </p>
                <p class="text-xs text-gray-400">{{ $certExpiry->format('d M Y') }}</p>
            @else
                <p class="text-sm font-semibold text-gray-400">Unknown</p>
            @endif
        </div>
    </div>

    {{-- Updates needed --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4">
        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center
            {{ $totalUpdates > 0 ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-green-100 dark:bg-green-900/30' }}">
            <x-filament::icon
                icon="{{ $totalUpdates > 0 ? 'heroicon-m-arrow-up-circle' : 'heroicon-m-check-badge' }}"
                class="h-6 w-6 {{ $totalUpdates > 0 ? 'text-amber-500' : 'text-green-600 dark:text-green-400' }}"
            />
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Updates</p>
            @if ($totalUpdates > 0)
                <p class="text-sm font-semibold text-amber-500">{{ $totalUpdates }} pending</p>
                <p class="text-xs text-gray-400">
                    @if($pluginUpdateCount) {{ $pluginUpdateCount }} plugin{{ $pluginUpdateCount > 1 ? 's' : '' }} @endif
                    @if($pluginUpdateCount && $themeUpdateCount) · @endif
                    @if($themeUpdateCount) {{ $themeUpdateCount }} theme{{ $themeUpdateCount > 1 ? 's' : '' }} @endif
                </p>
            @else
                <p class="text-sm font-semibold text-green-600 dark:text-green-400">All up to date</p>
            @endif
        </div>
    </div>

    {{-- Last Sync --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4">
        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900/30">
            <x-filament::icon icon="heroicon-m-arrow-path" class="h-6 w-6 text-blue-500 dark:text-blue-400" />
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Last Sync</p>
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $lastSync }}</p>
        </div>
    </div>

</div>


{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     ROW 2 — VERSION CARDS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

    {{-- WordPress --}}
    @php
        $wpLatest = $site->sitemeta?->wp_latest_version ?? null;
        $wpOutdated = $wpLatest && version_compare((string)$site->wp_ver, $wpLatest, '<');
    @endphp
    <a href="{{ route('filament.dashboard.resources.sites.core', ['record' => $site->id, 'tenant' => $tenant->uuid]) }}"
       class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4 hover:ring-primary-400 transition group">
        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center
            {{ $wpOutdated ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-indigo-100 dark:bg-indigo-900/30' }}">
            <x-filament::icon icon="icon-wordpress"
                class="h-6 w-6 {{ $wpOutdated ? 'text-amber-500' : 'text-indigo-500 dark:text-indigo-400' }}" />
        </div>
        <div class="min-w-0">
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">WordPress</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $site->wp_ver ?? '—' }}</p>
            @if($wpOutdated)
                <p class="text-xs text-amber-500">→ {{ $wpLatest }}</p>
            @else
                <p class="text-xs text-gray-400">Up to date</p>
            @endif
        </div>
    </a>

    {{-- PHP --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4">
        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center bg-violet-100 dark:bg-violet-900/30">
            <svg class="h-5 w-5 text-violet-600 dark:text-violet-400" viewBox="0 -1 100 50" fill="currentColor">
                <path d="m7.579 10.123 14.204 0c4.169 0.035 7.19 1.237 9.063 3.604 1.873 2.367 2.491 5.6 1.855 9.699-0.247 1.873-0.795 3.71-1.643 5.512-0.813 1.802-1.943 3.427-3.392 4.876-1.767 1.837-3.657 3.003-5.671 3.498-2.014 0.495-4.099 0.742-6.254 0.742l-6.36 0-2.014 10.07-7.367 0 7.579-38.001 0 0m6.201 6.042-3.18 15.9c0.212 0.035 0.424 0.053 0.636 0.053 0.247 0 0.495 0 0.742 0 3.392 0.035 6.219-0.3 8.48-1.007 2.261-0.742 3.781-3.321 4.558-7.738 0.636-3.71 0-5.848-1.908-6.413-1.873-0.565-4.222-0.83-7.049-0.795-0.424 0.035-0.83 0.053-1.219 0.053-0.353 0-0.724 0-1.113 0l0.053-0.053"/>
                <path d="m41.093 0 7.314 0-2.067 10.123 6.572 0c3.604 0.071 6.289 0.813 8.056 2.226 1.802 1.413 2.332 4.099 1.59 8.056l-3.551 17.649-7.42 0 3.392-16.854c0.353-1.767 0.247-3.021-0.318-3.763-0.565-0.742-1.784-1.113-3.657-1.113l-5.883-0.053-4.346 21.783-7.314 0 7.632-38.054 0 0"/>
                <path d="m70.412 10.123 14.204 0c4.169 0.035 7.19 1.237 9.063 3.604 1.873 2.367 2.491 5.6 1.855 9.699-0.247 1.873-0.795 3.71-1.643 5.512-0.813 1.802-1.943 3.427-3.392 4.876-1.767 1.837-3.657 3.003-5.671 3.498-2.014 0.495-4.099 0.742-6.254 0.742l-6.36 0-2.014 10.07-7.367 0 7.579-38.001 0 0m6.201 6.042-3.18 15.9c0.212 0.035 0.424 0.053 0.636 0.053 0.247 0 0.495 0 0.742 0 3.392 0.035 6.219-0.3 8.48-1.007 2.261-0.742 3.781-3.321 4.558-7.738 0.636-3.71 0-5.848-1.908-6.413-1.873-0.565-4.222-0.83-7.049-0.795-0.424 0.035-0.83 0.053-1.219 0.053-0.353 0-0.724 0-1.113 0l0.053-0.053"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">PHP (webserver)</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $site->php_ver ?? '—' }}</p>
            <p class="text-xs text-gray-400">FPM runtime</p>
        </div>
    </div>

    {{-- Plugins --}}
    <a href="{{ route('filament.dashboard.resources.sites.plugins', ['record' => $site->id, 'tenant' => $tenant->uuid]) }}"
       class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4 hover:ring-primary-400 transition">
        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center bg-sky-100 dark:bg-sky-900/30">
            <x-filament::icon icon="heroicon-m-puzzle-piece" class="h-6 w-6 text-sky-500 dark:text-sky-400" />
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Plugins</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $pluginCount }} installed</p>
            @if($pluginUpdateCount > 0)
                <p class="text-xs text-amber-500">{{ $pluginUpdateCount }} update{{ $pluginUpdateCount > 1 ? 's' : '' }} available</p>
            @else
                <p class="text-xs text-gray-400">All up to date</p>
            @endif
        </div>
    </a>

    {{-- Themes --}}
    <a href="{{ route('filament.dashboard.resources.sites.themes', ['record' => $site->id, 'tenant' => $tenant->uuid]) }}"
       class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4 hover:ring-primary-400 transition">
        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center bg-pink-100 dark:bg-pink-900/30">
            <x-filament::icon icon="heroicon-m-swatch" class="h-6 w-6 text-pink-500 dark:text-pink-400" />
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Themes</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $themeCount }} installed</p>
            @if($themeUpdateCount > 0)
                <p class="text-xs text-amber-500">{{ $themeUpdateCount }} update{{ $themeUpdateCount > 1 ? 's' : '' }} available</p>
            @else
                <p class="text-xs text-gray-400">All up to date</p>
            @endif
        </div>
    </a>

</div>


{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     ROW 3 — DETAILS + STORAGE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Site & Server info --}}
    <x-filament::section>
        <x-slot name="heading">Site Details</x-slot>

        <dl class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">

            @php
                $rows = [
                    ['icon' => 'heroicon-m-globe-alt',     'label' => 'URL',            'value' => $site->url,            'link' => $site->url],
                    ['icon' => 'heroicon-m-user-circle',   'label' => 'Client',         'value' => $client?->name ?? '—'],
                    ['icon' => 'heroicon-m-server-stack',  'label' => 'Server',         'value' => ($server?->name ?? '—') . ' (' . ($server?->ip ?? '') . ')'],
                    ['icon' => 'heroicon-m-folder-open',   'label' => 'Path',           'value' => $site->dir_path,       'mono' => true],
                    ['icon' => 'heroicon-m-circle-stack',  'label' => 'DB Prefix',      'value' => $site->db_prefix ?? '—', 'mono' => true],
                    ['icon' => 'heroicon-m-command-line',  'label' => 'WP-CLI',         'value' => $site->cli_ver ? 'v' . $site->cli_ver : '—'],
                ];

                if ($domainExpiry) {
                    $rows[] = [
                        'icon'  => 'heroicon-m-calendar-days',
                        'label' => 'Domain Expires',
                        'value' => $domainExpiry->format('d M Y') . ' (' . $domainDaysLeft . 'd)',
                        'warn'  => $domainDaysLeft < 30,
                    ];
                }
            @endphp

            @foreach($rows as $row)
            <div class="flex items-center gap-3 py-2.5">
                <x-filament::icon icon="{{ $row['icon'] }}"
                    class="h-4 w-4 flex-shrink-0 text-gray-400 dark:text-gray-500" />
                <span class="w-28 flex-shrink-0 text-gray-500 dark:text-gray-400">{{ $row['label'] }}</span>
                @if(isset($row['link']))
                    <a href="{{ $row['link'] }}" target="_blank" rel="noopener"
                       class="text-primary-600 dark:text-primary-400 hover:underline truncate {{ isset($row['mono']) ? 'font-mono text-xs' : '' }}">
                        {{ $row['value'] }}
                    </a>
                @else
                    <span class="truncate font-medium text-gray-800 dark:text-gray-100
                        {{ isset($row['mono']) ? 'font-mono text-xs' : '' }}
                        {{ isset($row['warn']) && $row['warn'] ? 'text-amber-500' : '' }}">
                        {{ $row['value'] }}
                    </span>
                @endif
            </div>
            @endforeach

        </dl>
    </x-filament::section>

    {{-- Storage --}}
    <x-filament::section>
        <x-slot name="heading">Storage & Database</x-slot>

        @php
            $dirMb  = $site->dir_size ?? 0;
            $dbMb   = $meta?->db_size ?? 0;
            $filesMb = max(0, $dirMb - $dbMb);
            $totalMb = $dirMb ?: 1;
            $dbPct   = $totalMb ? round($dbMb / $totalMb * 100) : 0;
            $filePct = 100 - $dbPct;
        @endphp

        {{-- Size summary --}}
        <div class="flex gap-6 mb-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Directory</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $site->getFormatedDBSize() ?? '—' }}</p>
            </div>
            <div class="border-l border-gray-200 dark:border-gray-700 pl-6">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Database</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $site->getDBSize() ?? '—' }}</p>
            </div>
            <div class="border-l border-gray-200 dark:border-gray-700 pl-6">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">DB Tables</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ count($dbTables) }}</p>
            </div>
        </div>

        {{-- Stacked bar --}}
        @if($totalMb > 1)
        <div class="mb-4">
            <div class="flex h-3 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 gap-0.5">
                <div class="bg-violet-500 rounded-l-full transition-all" style="width:{{ $dbPct }}%"
                     title="Database: {{ $dbMb }} MB ({{ $dbPct }}%)"></div>
                <div class="bg-sky-400 rounded-r-full flex-1"
                     title="Files: {{ $filesMb }} MB ({{ $filePct }}%)"></div>
            </div>
            <div class="flex gap-4 mt-2 text-xs text-gray-500">
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-2.5 h-2.5 rounded-sm bg-violet-500"></span> Database {{ $dbPct }}%
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-2.5 h-2.5 rounded-sm bg-sky-400"></span> Files {{ $filePct }}%
                </span>
            </div>
        </div>
        @endif

        {{-- Top DB tables by size --}}
        @if(count($dbTables))
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Largest Tables</p>
        <div class="space-y-1.5">
            @php
                $sortedTables = collect($dbTables)
                    ->map(fn($t) => ['name' => $t['Name'], 'mb' => (int) $t['Size']])
                    ->sortByDesc('mb')
                    ->take(8)
                    ->values();
                $maxMb = $sortedTables->first()['mb'] ?? 1;
                $maxMb = max($maxMb, 1);
            @endphp
            @foreach($sortedTables as $tbl)
            <div class="flex items-center gap-2 text-xs">
                <span class="w-40 truncate font-mono text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $tbl['name'] }}</span>
                <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                    <div class="bg-violet-400 h-1.5 rounded-full"
                         style="width:{{ $maxMb > 0 ? round($tbl['mb']/$maxMb*100) : 0 }}%"></div>
                </div>
                <span class="w-10 text-right text-gray-500">{{ $tbl['mb'] ?: '<1' }} MB</span>
            </div>
            @endforeach
        </div>
        @endif

    </x-filament::section>

</div>


{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     ROW 4 — ALL DB TABLES (collapsible)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
@if(count($dbTables))
<div x-data="{ open: false }">
    <button @click="open = !open"
        class="w-full flex items-center justify-between px-5 py-3 rounded-xl bg-white dark:bg-gray-900
               ring-1 ring-gray-950/5 dark:ring-white/10 text-sm font-medium text-gray-600 dark:text-gray-300
               hover:bg-gray-50 dark:hover:bg-gray-800 transition">
        <span class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-circle-stack" class="h-4 w-4 text-gray-400" />
            All Database Tables
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-full px-2 py-0.5">
                {{ count($dbTables) }}
            </span>
        </span>
        <x-filament::icon icon="heroicon-m-chevron-down" class="h-4 w-4 text-gray-400 transition-transform"
            ::class="open ? 'rotate-180' : ''" />
    </button>

    <div x-show="open" x-collapse class="mt-1">
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Table</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Size</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                        @foreach($dbTables as $tbl)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                            <td class="px-5 py-2 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $tbl['Name'] }}</td>
                            <td class="px-5 py-2 text-right text-xs text-gray-500">
                                {{ $tbl['Size'] === '0 MB' ? '< 1 MB' : $tbl['Size'] }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
