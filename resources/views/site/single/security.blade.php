@extends('site/single/pagetemplate')

@section('content')

    @if ( $this->getRecord()->is_staging )

    <x-filament::section>
        <x-slot name="heading">Feature not active</x-slot>
        <x-slot name="headerEnd">Staging detected</x-slot>
        One or more features are not available in the staging mode. This website is detected to be a staging.
    </x-filament::section>

    @elseif( !$this->getRecord()->ssh_connection )
        @include('site/notifications/general')
    @else

    @php
        $allVulns       = $this->getAllVulnerabilities();
        $coreVulns      = $this->getCoreVulnerabilities();
        $inactivePlugins = $this->getInactivePlugins();
        $vulnCount      = count($allVulns);
        $inactiveCount  = $inactivePlugins->count();
        $wpVersion      = $this->getRecord()->wp_ver ?? 'Unknown';
        $hasCoreVulns   = count($coreVulns) > 0;

        $severityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
        usort($allVulns, function ($a, $b) use ($severityOrder) {
            $aOrder = $severityOrder[strtolower($a['severity'] ?? '')] ?? 4;
            $bOrder = $severityOrder[strtolower($b['severity'] ?? '')] ?? 4;
            return $aOrder <=> $bOrder;
        });
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        {{-- Total Vulnerabilities --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4">
            <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center
                {{ $vulnCount > 0 ? 'bg-red-100 dark:bg-red-900/30' : 'bg-green-100 dark:bg-green-900/30' }}">
                <x-filament::icon
                    icon="{{ $vulnCount > 0 ? 'heroicon-m-shield-exclamation' : 'heroicon-m-shield-check' }}"
                    class="h-6 w-6 {{ $vulnCount > 0 ? 'text-red-500 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}"
                />
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Vulnerabilities</p>
                <p class="text-lg font-bold {{ $vulnCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                    {{ $vulnCount }}
                </p>
            </div>
        </div>

        {{-- Inactive Plugins --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4">
            <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center
                {{ $inactiveCount > 0 ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-green-100 dark:bg-green-900/30' }}">
                <x-filament::icon
                    icon="{{ $inactiveCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle' }}"
                    class="h-6 w-6 {{ $inactiveCount > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-green-600 dark:text-green-400' }}"
                />
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Inactive Plugins</p>
                <p class="text-lg font-bold {{ $inactiveCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400' }}">
                    {{ $inactiveCount }}
                </p>
            </div>
        </div>

        {{-- WordPress Version --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 px-5 py-4 flex items-center gap-4">
            <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center
                {{ $hasCoreVulns ? 'bg-red-100 dark:bg-red-900/30' : 'bg-green-100 dark:bg-green-900/30' }}">
                <x-filament::icon
                    icon="{{ $hasCoreVulns ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle' }}"
                    class="h-6 w-6 {{ $hasCoreVulns ? 'text-red-500 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}"
                />
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">WordPress</p>
                <p class="text-lg font-bold {{ $hasCoreVulns ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-200' }}">
                    {{ $wpVersion }}
                </p>
                @if ($hasCoreVulns)
                    <p class="text-xs text-red-500 dark:text-red-400">Core vulnerabilities detected</p>
                @endif
            </div>
        </div>

    </div>

    {{-- Vulnerabilities Table --}}
    <x-filament::section class="mb-6">
        <x-slot name="heading">Vulnerabilities</x-slot>
        <x-slot name="headerEnd">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $vulnCount }} {{ Str::plural('issue', $vulnCount) }} found</span>
        </x-slot>

        @if ($vulnCount > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Severity</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Component</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Type</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Installed Version</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Vulnerability</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fixed In</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($allVulns as $vuln)
                            @php
                                $severity = strtolower($vuln['severity'] ?? 'unknown');
                                $severityColors = [
                                    'critical' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                    'high'     => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                                    'medium'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                    'low'      => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                    'unknown'  => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                ];
                                $badgeClass = $severityColors[$severity] ?? $severityColors['unknown'];

                                $typeColors = [
                                    'plugin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                                    'theme'  => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
                                    'core'   => 'bg-pink-100 text-pink-800 dark:bg-pink-900/40 dark:text-pink-300',
                                ];
                                $typeBadge = $typeColors[$vuln['type'] ?? ''] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                        {{ ucfirst($severity) }}
                                        @if ($vuln['cvss_score'])
                                            <span class="opacity-75">({{ $vuln['cvss_score'] }})</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-3 py-3 font-medium text-gray-900 dark:text-gray-100">
                                    {{ $vuln['component'] }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $typeBadge }}">
                                        {{ ucfirst($vuln['type']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $vuln['version'] ?? '\u2014' }}
                                </td>
                                <td class="px-3 py-3 text-gray-700 dark:text-gray-300 max-w-xs">
                                    <div class="font-medium">{{ $vuln['vuln_name'] }}</div>
                                    @if (!empty($vuln['description']))
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ $vuln['description'] }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    @if ($vuln['max_version'])
                                        <span class="font-medium text-green-600 dark:text-green-400">{{ $vuln['max_version'] }}</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">No fix available</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex items-center gap-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <x-filament::icon
                    icon="heroicon-m-shield-check"
                    class="h-6 w-6 text-green-600 dark:text-green-400 flex-shrink-0"
                />
                <div>
                    <p class="font-semibold text-green-800 dark:text-green-300">All Clear</p>
                    <p class="text-sm text-green-700 dark:text-green-400">No known vulnerabilities detected in your plugins, themes, or WordPress core.</p>
                </div>
            </div>
        @endif
    </x-filament::section>

    {{-- Inactive Plugins --}}
    <x-filament::section>
        <x-slot name="heading">Inactive Plugins</x-slot>
        <x-slot name="headerEnd">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $inactiveCount }} {{ Str::plural('plugin', $inactiveCount) }}</span>
        </x-slot>

        @if ($inactiveCount > 0)
            <div class="flex items-start gap-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-4 mb-4">
                <x-filament::icon
                    icon="heroicon-m-exclamation-triangle"
                    class="h-5 w-5 text-amber-500 dark:text-amber-400 flex-shrink-0 mt-0.5"
                />
                <p class="text-sm text-amber-800 dark:text-amber-300">
                    Inactive plugins can still pose security risks. They may contain unpatched vulnerabilities and provide an attack surface even when deactivated. Consider removing plugins you no longer use.
                </p>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($inactivePlugins as $plugin)
                    <div class="flex items-center justify-between py-3 px-1">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-8 w-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <x-filament::icon
                                    icon="heroicon-m-puzzle-piece"
                                    class="h-4 w-4 text-gray-400 dark:text-gray-500"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $plugin->title ?: $plugin->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $plugin->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($plugin->pivot->version)
                                <span class="text-xs text-gray-500 dark:text-gray-400">v{{ $plugin->pivot->version }}</span>
                            @endif
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                Inactive
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex items-center gap-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <x-filament::icon
                    icon="heroicon-m-check-circle"
                    class="h-6 w-6 text-green-600 dark:text-green-400 flex-shrink-0"
                />
                <p class="text-sm text-green-700 dark:text-green-400">No inactive plugins found. All installed plugins are active.</p>
            </div>
        @endif
    </x-filament::section>

    @endif

@endsection
