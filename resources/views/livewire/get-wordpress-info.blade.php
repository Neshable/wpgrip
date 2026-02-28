<div>
    @if ($loading)
        <div class="flex items-center justify-center py-12">
            <x-filament::loading-indicator class="h-8 w-8 text-primary-500" />
            <span class="ml-3 text-gray-500 dark:text-gray-400">Loading WordPress version info…</span>
        </div>
    @elseif ($apiError)
        <x-filament::section>
            <x-slot name="heading">WordPress Core</x-slot>
            <p class="text-gray-500 dark:text-gray-400">Could not reach the WordPress API. Please check your internet connection.</p>
        </x-filament::section>
    @else

        {{-- ── STATUS CARD ─────────────────────────────────────────────────── --}}
        @php
            $siteVer     = (string) $this->site->wp_ver;
            $isUpdating  = $this->site->status && $this->site->status == \App\Enums\SiteStatus::UpdatingCore;
            $needsUpdate = $latestVersion && version_compare($siteVer, $latestVersion, '<');
        @endphp

        <x-filament::section
            :icon="$needsUpdate ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'"
            :icon-color="$needsUpdate ? 'warning' : 'success'"
        >
            <x-slot name="heading">
                @if ($isUpdating)
                    WordPress is being updated…
                @elseif ($needsUpdate)
                    WordPress update available
                @else
                    WordPress is up to date
                @endif
            </x-slot>

            <x-slot name="headerEnd">
                @if ($isUpdating)
                    <span class="flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400 font-medium">
                        <x-filament::loading-indicator class="h-5 w-5" />
                        Updating…
                    </span>
                    <script>setTimeout(() => location.reload(), 8000);</script>
                @else
                    {{-- Update to latest --}}
                    @if ($needsUpdate)
                        {{ ($this->updateAction)(['site_id' => $this->site->id]) }}
                    @endif

                    {{-- Switch / Rollback --}}
                    {{ ($this->rollbackAction)(['site_id' => $this->site->id]) }}

                    {{-- Re-sync --}}
                    {{ ($this->syncVersionAction)(['site_id' => $this->site->id]) }}
                @endif

                <x-filament-actions::modals />
            </x-slot>

            {{-- Version pills ------------------------------------------------- --}}
            <div class="flex flex-wrap gap-4 mt-1">
                <div class="flex flex-col items-start">
                    <span class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Installed</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold
                        {{ $needsUpdate
                            ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'
                            : 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' }}">
                        <x-filament::icon icon="{{ $needsUpdate ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check' }}" class="w-4 h-4" />
                        {{ $siteVer ?: '—' }}
                    </span>
                </div>

                @if ($latestVersion)
                <div class="flex flex-col items-start">
                    <span class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Latest</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold
                        bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                        <x-filament::icon icon="heroicon-m-arrow-up" class="w-4 h-4" />
                        {{ $latestVersion }}
                    </span>
                </div>
                @endif

                @if ($needsUpdate)
                <div class="flex items-end pb-0.5">
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">
                        Update available: {{ $siteVer }} → {{ $latestVersion }}
                    </span>
                </div>
                @endif
            </div>

            @if ($needsUpdate)
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                Your site is running an outdated version of WordPress. Keeping WordPress updated improves security and performance. Click <strong>Update to {{ $latestVersion }}</strong> above or use <strong>Switch version</strong> to pick a specific release.
            </p>
            @endif

        </x-filament::section>

        {{-- ── VERSION HISTORY TABLE ──────────────────────────────────────── --}}
        <x-filament::section class="mt-4">
            <x-slot name="heading">WordPress Version History</x-slot>
            <x-slot name="description">All available WordPress releases. Use <em>Switch version</em> to upgrade or rollback.</x-slot>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Version</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Min PHP</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Min MySQL</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Download</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($allVersions as $v)
                            @php
                                $isInstalled = (string)$v['version'] === $siteVer;
                                $isLatest    = $v['version'] === $latestVersion;
                                $isNewer     = version_compare($v['version'], $siteVer, '>');
                                $isOlder     = version_compare($v['version'], $siteVer, '<');
                            @endphp
                            <tr class="
                                @if ($isInstalled) bg-blue-50 dark:bg-blue-900/20 font-semibold
                                @elseif ($isNewer)  bg-green-50/40 dark:bg-green-900/10
                                @else               hover:bg-gray-50 dark:hover:bg-gray-800/50
                                @endif
                            ">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $isInstalled ? 'text-blue-700 dark:text-blue-300' : 'text-gray-900 dark:text-gray-100' }}">
                                            {{ $v['version'] }}
                                        </span>
                                        @if ($isInstalled)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200">
                                                Installed
                                            </span>
                                        @endif
                                        @if ($isLatest)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200">
                                                Latest
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($isInstalled)
                                        <span class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 text-xs font-medium">
                                            <x-filament::icon icon="heroicon-m-check-circle" class="w-4 h-4" /> Current
                                        </span>
                                    @elseif ($isNewer)
                                        <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400 text-xs font-medium">
                                            <x-filament::icon icon="heroicon-m-arrow-up-circle" class="w-4 h-4" /> Upgrade
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 text-xs font-medium">
                                            <x-filament::icon icon="heroicon-m-arrow-down-circle" class="w-4 h-4" /> Rollback
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $v['php_version'] }}+</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $v['mysql_version'] }}+</td>
                                <td class="px-4 py-3">
                                    <a href="{{ $v['download'] }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 hover:underline text-xs">
                                        <x-filament::icon icon="heroicon-m-arrow-down-tray" class="w-3.5 h-3.5" />
                                        .zip
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

    @endif
</div>
