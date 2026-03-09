@extends('site/single/pagetemplate')

@php
    $tenant = Filament\Facades\Filament::getTenant();
    $stagingSite = $this->getStagingSite();
    $latestSync = $this->getLatestSync();
    $canSync = $this->canSync();
    $isSyncRunning = $this->isSyncRunning();
@endphp

@section('content')

<x-filament::section>
    <x-slot name="heading">
        Staging Site
    </x-slot>

    <x-slot name="description">
        Manage your staging environment and sync data from production.
    </x-slot>

    <x-slot name="headerEnd">
        @if($stagingSite && $canSync)
            {{ $this->syncLiveToStaging() }}
        @endif
    </x-slot>

    @if($stagingSite)
        {{-- Staging site details --}}
        <div class="space-y-1">
            @php
                $fields = [
                    'Staging URL' => $stagingSite->url,
                    'SSH User' => $stagingSite->ssh_user,
                    'Directory' => $stagingSite->dir_path,
                    'Server' => $stagingSite->server->name . ' (' . $stagingSite->server->ip . ')',
                    'Status' => $stagingSite->status,
                    'Last Synced' => $stagingSite->last_sync
                        ? \Carbon\Carbon::parse($stagingSite->last_sync)->diffForHumans()
                        : 'Never',
                ];
            @endphp

            @each('site.listing.simple', $fields, 'field')
        </div>

        {{-- Sync status section --}}
        @if($latestSync)
            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                    Latest Sync
                </h3>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            {{-- Status icon --}}
                            @if($latestSync->status === 'completed')
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <x-heroicon-s-check-circle class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                            @elseif($latestSync->status === 'failed')
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                    <x-heroicon-s-x-circle class="w-5 h-5 text-red-600 dark:text-red-400" />
                                </div>
                            @else
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <x-heroicon-s-arrow-path class="w-5 h-5 text-blue-600 dark:text-blue-400 animate-spin" />
                                </div>
                            @endif

                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    @switch($latestSync->status)
                                        @case('pending')
                                            Waiting to start…
                                            @break
                                        @case('preflight')
                                            Running preflight checks…
                                            @break
                                        @case('syncing_db')
                                            Syncing database…
                                            @break
                                        @case('syncing_uploads')
                                            Syncing uploads…
                                            @break
                                        @case('replacing')
                                            Replacing URLs…
                                            @break
                                        @case('cleanup')
                                            Running cleanup…
                                            @break
                                        @case('completed')
                                            Sync completed
                                            @break
                                        @case('failed')
                                            Sync failed
                                            @break
                                    @endswitch
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $latestSync->created_at->diffForHumans() }}
                                    @if($latestSync->strategy)
                                        &middot; {{ ucfirst($latestSync->strategy) }} strategy
                                    @endif
                                    @if($latestSync->duration && $latestSync->completed_at)
                                        &middot; {{ $latestSync->duration }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Size badges --}}
                        <div class="flex gap-2">
                            @if($latestSync->sync_db)
                                <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/20 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300">
                                    DB
                                    @if($latestSync->db_size_bytes)
                                        ({{ Number::fileSize($latestSync->db_size_bytes, precision: 1) }})
                                    @endif
                                </span>
                            @endif
                            @if($latestSync->sync_uploads)
                                <span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-900/20 px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-300">
                                    Uploads
                                    @if($latestSync->uploads_size_bytes)
                                        ({{ Number::fileSize($latestSync->uploads_size_bytes, precision: 1) }})
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Error message --}}
                    @if($latestSync->status === 'failed' && $latestSync->error_message)
                        <div class="mt-3 rounded-md bg-red-50 dark:bg-red-900/20 p-3">
                            <p class="text-xs text-red-700 dark:text-red-300 font-mono">
                                {{ Str::limit($latestSync->error_message, 300) }}
                            </p>
                        </div>
                    @endif

                    {{-- Progress bar for running syncs --}}
                    @if($latestSync->isRunning())
                        @php
                            $steps = ['pending', 'preflight', 'syncing_db', 'syncing_uploads', 'replacing', 'cleanup'];
                            $currentIndex = array_search($latestSync->status, $steps);
                            $progress = $currentIndex !== false ? (($currentIndex + 1) / count($steps)) * 100 : 0;
                        @endphp
                        <div class="mt-3">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Upgrade prompt for non-Agency users --}}
        @if(!$canSync)
            <div class="mt-6 rounded-lg border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 p-4">
                <div class="flex items-start gap-3">
                    <x-heroicon-s-lock-closed class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" />
                    <div>
                        <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                            Production → Staging Sync
                        </p>
                        <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                            One-click sync of your production database and uploads to staging is available on Agency and Enterprise plans.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- No staging site --}}
        <div class="py-8 text-center">
            <x-heroicon-o-beaker class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500" />
            <h3 class="mt-3 text-sm font-semibold text-gray-900 dark:text-gray-100">No staging environment</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                To create a staging environment, add a new site and set this site as the production parent.
            </p>
        </div>
    @endif

    <x-filament-actions::modals />
</x-filament::section>

@if($isSyncRunning)
    @push('scripts')
        <script>
            // Auto-refresh every 5 seconds while sync is running
            setTimeout(function() {
                window.location.reload();
            }, 5000);
        </script>
    @endpush
@endif

@endsection
