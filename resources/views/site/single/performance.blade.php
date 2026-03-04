@extends('site/single/pagetemplate')

@section('content')
@php
    use App\Services\Helpers\BladeHelper;

    $tenant = Filament\Facades\Filament::getTenant();

    $desktop = \App\Models\PerformanceData::where('site_id', $this->record->id)
        ->where('strategy', 'desktop')
        ->orderBy('created_at', 'desc')
        ->first();

    $mobile = \App\Models\PerformanceData::where('site_id', $this->record->id)
        ->where('strategy', 'mobile')
        ->orderBy('created_at', 'desc')
        ->first();

    $hasData = $desktop || $mobile;

    // Helper: format ms values
    $ms = fn($v) => $v !== null ? round($v / 1000, 2).'s' : '—';

    // Metric thresholds: [good_max, needs_improvement_max]
    $thresholds = [
        'fcp'                  => [1800, 3000],
        'lcp'                  => [2500, 4000],
        'speed_index'          => [3400, 5800],
        'total_blocking_time'  => [200,  600],
        'time_interactive'     => [3800, 7300],
        'server_response_time' => [200,  600],
    ];

    $metricColor = function($key, $val) use ($thresholds) {
        if ($val === null) return ['dot' => 'bg-gray-400', 'text' => 'text-gray-400', 'badge' => 'bg-gray-500/10 text-gray-400'];
        [$good, $needs] = $thresholds[$key] ?? [PHP_INT_MAX, PHP_INT_MAX];
        if ($val <= $good)  return ['dot' => 'bg-emerald-400', 'text' => 'text-emerald-400', 'badge' => 'bg-emerald-500/10 text-emerald-400'];
        if ($val <= $needs) return ['dot' => 'bg-amber-400',   'text' => 'text-amber-400',   'badge' => 'bg-amber-500/10 text-amber-400'];
        return                     ['dot' => 'bg-red-400',     'text' => 'text-red-400',      'badge' => 'bg-red-500/10 text-red-400'];
    };

    $scoreColor = function($score) {
        if ($score === null) return ['ring' => 'ring-gray-500/30',    'text' => 'text-gray-400',    'bar' => 'bg-gray-500',    'label' => 'No data',   'badge' => 'bg-gray-500/10 text-gray-400',    'hex' => '#6b7280'];
        if ($score <= 49)    return ['ring' => 'ring-red-500/40',     'text' => 'text-red-400',     'bar' => 'bg-red-500',     'label' => 'Poor',      'badge' => 'bg-red-500/10 text-red-400',      'hex' => '#f87171'];
        if ($score <= 89)    return ['ring' => 'ring-amber-500/40',   'text' => 'text-amber-400',   'bar' => 'bg-amber-500',   'label' => 'Moderate',  'badge' => 'bg-amber-500/10 text-amber-400',  'hex' => '#fbbf24'];
        return                      ['ring' => 'ring-emerald-500/40', 'text' => 'text-emerald-400', 'bar' => 'bg-emerald-500', 'label' => 'Good',      'badge' => 'bg-emerald-500/10 text-emerald-400','hex' => '#34d399'];
    };
@endphp


{{-- Header row: title + Run Test button --}}
<div class="flex items-center justify-between mb-5 mt-1">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Google PageSpeed</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            Powered by <a href="https://pagespeed.web.dev" target="_blank" class="text-blue-400 hover:underline">Google PageSpeed Insights</a> (Lighthouse v11)
        </p>
    </div>
    <div class="flex items-center gap-3">
        @if($hasData)
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Last run: {{ ($desktop ?? $mobile)->created_at->diffForHumans() }}
            </span>
        @endif
        <x-filament::button
            wire:click="runPageSpeedTest"
            wire:loading.attr="disabled"
            icon="heroicon-m-bolt"
            size="sm"
        >
            <span wire:loading.remove wire:target="runPageSpeedTest">Run PageSpeed Test</span>
            <span wire:loading wire:target="runPageSpeedTest">Running…</span>
        </x-filament::button>
    </div>
</div>

@if(!$hasData)
{{-- Empty state --}}
<div class="rounded-xl border border-dashed border-gray-700/60 bg-gray-900/30 px-8 py-16 flex flex-col items-center text-center">
    <div class="w-14 h-14 rounded-full bg-blue-500/10 flex items-center justify-center mb-4">
        <x-filament::icon icon="heroicon-o-presentation-chart-line" class="w-7 h-7 text-blue-400" />
    </div>
    <h3 class="text-base font-semibold text-gray-200 mb-1">No performance data yet</h3>
    <p class="text-sm text-gray-400 max-w-sm mb-5">
        Click <strong class="text-gray-200">Run PageSpeed Test</strong> above to analyse this site using Google Lighthouse. Tests also run automatically every night.
    </p>
    @if(!env('GOOGLE_API_PAGESPEED'))
    <div class="rounded-lg bg-amber-500/10 border border-amber-500/20 px-4 py-3 text-left max-w-md">
        <p class="text-xs font-medium text-amber-300 mb-1">⚠ API key not configured</p>
        <p class="text-xs text-amber-300/70">Set <code class="font-mono bg-amber-900/30 px-1 rounded">GOOGLE_API_PAGESPEED</code> in your <code class="font-mono bg-amber-900/30 px-1 rounded">.env</code> file to enable live tests.</p>
    </div>
    @endif
</div>
@else

{{-- Score cards row: Desktop + Mobile --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    @foreach([['label' => 'Desktop', 'icon' => 'heroicon-m-computer-desktop', 'record' => $desktop],
              ['label' => 'Mobile',  'icon' => 'heroicon-m-device-phone-mobile', 'record' => $mobile]] as $panel)
    @php
        $rec   = $panel['record'];
        $score = $rec ? round($rec->performance) : null;
        $c     = $scoreColor($score);
        $pct   = $score ?? 0;
    @endphp
    <div class="rounded-xl ring-1 {{ $c['ring'] }} bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-white/5 p-5">
        {{-- Panel header --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="{{ $panel['icon'] }}" class="w-4 h-4 text-gray-400" />
                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $panel['label'] }}</span>
            </div>
            @if($rec)
            <span class="text-xs {{ $c['badge'] }} px-2 py-0.5 rounded-full font-medium">{{ $c['label'] }}</span>
            @else
            <span class="text-xs bg-gray-200 dark:bg-gray-700/40 text-gray-500 px-2 py-0.5 rounded-full">No data</span>
            @endif
        </div>

        {{-- Score gauge --}}
        <div class="flex items-center gap-5 mb-5">
            {{-- SVG ring gauge --}}
            <div class="relative flex-shrink-0 w-20 h-20">
                <svg viewBox="0 0 36 36" class="w-20 h-20 -rotate-90">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-300 dark:text-gray-700/60" />
                    @if($score !== null)
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-dasharray="{{ $pct }}, 100"
                        stroke="{{ $c['hex'] }}"
                    />
                    @endif
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center rotate-0">
                    <span class="text-2xl font-bold {{ $c['text'] }}">{{ $score ?? '—' }}</span>
                </div>
            </div>
            {{-- Score bar --}}
            <div class="flex-1">
                <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                    <span>0</span><span>50</span><span>90</span><span>100</span>
                </div>
                <div class="relative h-2 bg-gray-200 dark:bg-gray-700/60 rounded-full overflow-hidden">
                    {{-- colour zones --}}
                    <div class="absolute inset-y-0 left-0 w-[50%] bg-red-500/20 rounded-l-full"></div>
                    <div class="absolute inset-y-0 left-[50%] w-[40%] bg-amber-500/20"></div>
                    <div class="absolute inset-y-0 left-[90%] right-0 bg-emerald-500/20 rounded-r-full"></div>
                    {{-- pointer --}}
                    @if($score !== null)
                    <div class="absolute top-0 bottom-0 w-0.5 bg-white rounded-full shadow-[0_0_4px_rgba(255,255,255,0.6)]"
                         style="left: {{ $pct }}%"></div>
                    @endif
                </div>
                <div class="flex justify-between text-xs mt-1.5">
                    <span class="text-red-400">Poor</span>
                    <span class="text-amber-400">Moderate</span>
                    <span class="text-emerald-400">Good</span>
                </div>
                @if($rec)
                <p class="text-xs text-gray-500 mt-2">Tested {{ $rec->created_at->format('M j, Y · H:i') }}</p>
                @endif
            </div>
        </div>

        @if($rec)
        {{-- Core Web Vitals metrics --}}
        <div class="border-t border-gray-200 dark:border-white/5 pt-4 grid grid-cols-2 gap-x-6 gap-y-3">
            @foreach([
                ['key' => 'fcp',                 'label' => 'First Contentful Paint',  'val' => $rec->fcp,                 'fmt' => 'ms'],
                ['key' => 'lcp',                 'label' => 'Largest Contentful Paint','val' => $rec->lcp,                 'fmt' => 'ms'],
                ['key' => 'speed_index',         'label' => 'Speed Index',             'val' => $rec->speed_index,         'fmt' => 'ms'],
                ['key' => 'total_blocking_time', 'label' => 'Total Blocking Time',     'val' => $rec->total_blocking_time, 'fmt' => 'ms'],
                ['key' => 'time_interactive',    'label' => 'Time to Interactive',     'val' => $rec->time_interactive,    'fmt' => 'ms'],
                ['key' => 'server_response_time','label' => 'Server Response Time',    'val' => $rec->server_response_time,'fmt' => 'ms'],
            ] as $m)
            @php $mc = $metricColor($m['key'], $m['val']); @endphp
            <div class="flex items-start gap-2">
                <span class="mt-1 w-2 h-2 rounded-full flex-shrink-0 {{ $mc['dot'] }}"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 truncate">{{ $m['label'] }}</p>
                    <p class="text-sm font-semibold {{ $mc['text'] }}">
                        {{ $m['val'] !== null ? round($m['val'] / 1000, 2).'s' : '—' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- DOM Size --}}
        @if($rec->dom_size)
        <div class="border-t border-gray-200 dark:border-white/5 mt-3 pt-3 flex items-center justify-between">
            <span class="text-xs text-gray-400">DOM Size</span>
            <span class="text-xs font-mono text-gray-700 dark:text-gray-300">{{ number_format($rec->dom_size) }} elements
                @if($rec->dom_size > 1500)
                <span class="ml-1 text-amber-400 text-[10px]">⚠ large</span>
                @endif
            </span>
        </div>
        @endif

        @else
        <div class="border-t border-gray-200 dark:border-white/5 pt-4 text-center text-sm text-gray-500 py-4">
            No data — run a test to populate {{ $panel['label'] }} metrics.
        </div>
        @endif
    </div>
    @endforeach
</div>

{{-- Legend --}}
<div class="flex items-center gap-6 px-1 mb-6">
    <p class="text-xs text-gray-500">Score legend:</p>
    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span><span class="text-xs text-gray-400">0–49 Poor</span></div>
    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span><span class="text-xs text-gray-400">50–89 Moderate</span></div>
    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span><span class="text-xs text-gray-400">90–100 Good</span></div>
    <p class="text-xs text-gray-600 ml-auto">Values estimated · may vary</p>
</div>

@endif {{-- end hasData --}}
@endsection
