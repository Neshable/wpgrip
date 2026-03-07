
@php
    $monitor = $this->getRecord()->get_main_monitor();   
@endphp

@extends('site/single/pagetemplate')

@section('content')
    <div>
    </div>
        
    @if (  $monitor )

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <x-filament::section icon="heroicon-s-wrench" icon-color="{{ $monitor->certificate_status == 'valid' ? 'success' : 'warning'}}"> 
        
        <x-slot name="heading">
            SSL Certificate Info
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::button wire:click="checkSSL" outlined>
                Check SSL
            </x-filament::button>
        </x-slot> 

        <x-slot name="description">
            The monitor is scheduled to run every day.
        </x-slot>   

        {{-- Content --}}
        @if ( $monitor->certificate_status == 'valid' )
            The certificate is valid. Provided by {{ $monitor->certificate_issuer }} and it expires on {{ $monitor->certificate_expiration_date }}.
        @else
            The SSL certificate is not valid.   
        @endif
    </x-filament::section>

    <x-filament::section icon="heroicon-s-wrench" icon-color="{{ $monitor->uptime_status == 'up' ? 'success' : 'warning'}}"> 
        
        <x-slot name="heading">
            Uptime Monitor
        </x-slot>


        <x-slot name="headerEnd">
            <x-filament::button wire:click="checkUptime" outlined>
                Check now
            </x-filament::button>
        </x-slot> 
    
        <x-slot name="description">
            The monitor runs every 5 minutes. Last checked: {{ $monitor->uptime_last_check_date }}
        </x-slot>   

        {{-- Content --}}
        @if ( $monitor->uptime_status == 'up' )
            Website is up and running. 
        @else
            The website is currently down. Reason: {{ $monitor->uptime_check_failure_reason }}.  
        @endif
    </x-filament::section>

    </div>

    <div>
        @livewire(\App\Filament\Dashboard\Resources\SiteResource\Widgets\UptimeChart::class)
    </div>

    <div class="mt-6">
        @livewire(\App\Filament\Dashboard\Resources\SiteResource\Widgets\ResponseTimeChart::class)
    </div>

    {{-- Response time summary stats --}}
    @php
        $recentLogs = $this->getRecord()->monitorLogs()
            ->whereNotNull('response_time_ms')
            ->where('uptime_status', 'up')
            ->where('created_at', '>=', now()->subHours(24))
            ->get();
        $avgResponse = $recentLogs->count() > 0 ? round($recentLogs->avg('response_time_ms')) : null;
        $minResponse = $recentLogs->count() > 0 ? round($recentLogs->min('response_time_ms')) : null;
        $maxResponse = $recentLogs->count() > 0 ? round($recentLogs->max('response_time_ms')) : null;
        $avgTtfb = $recentLogs->count() > 0 ? round($recentLogs->avg('ttfb_ms'), 1) : null;
        $avgDns = $recentLogs->count() > 0 ? round($recentLogs->avg('dns_time_ms'), 1) : null;
        $avgTls = $recentLogs->count() > 0 ? round($recentLogs->avg('tls_time_ms'), 1) : null;
    @endphp

    @if($avgResponse !== null)
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-6">
        <x-filament::section>
            <div class="text-center">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avg Response</div>
                <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $avgResponse }}ms</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Min</div>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $minResponse }}ms</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Max</div>
                <div class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $maxResponse }}ms</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avg TTFB</div>
                <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $avgTtfb }}ms</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avg DNS</div>
                <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $avgDns }}ms</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avg TLS</div>
                <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $avgTls }}ms</div>
            </div>
        </x-filament::section>
    </div>
    @endif

    {{-- Monitors table --}}
    <x-filament::section>
        <x-slot name="heading">Monitors</x-slot>
        <x-slot name="description">List of all monitored URLs.</x-slot>
        @livewire('list-monitors', ['site_id' => $this->getRecord()->id])
    </x-filament::section>

    @endif
@endsection
