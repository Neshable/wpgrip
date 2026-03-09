@php
    $record = $getRecord();
    $monitor = $record->get_main_monitor() ?? null;

    // Response time data
    $logs = $record->monitorLogs()
        ->whereNotNull('response_time_ms')
        ->where('uptime_status', 'up')
        ->latest()
        ->limit(30)
        ->pluck('response_time_ms')
        ->reverse()
        ->values();

    $latest = $logs->last();
    $avg = $logs->count() > 0 ? round($logs->avg()) : null;

    // Determine color based on response time
    if ($latest === null) {
        $color = 'gray';
        $dotColor = 'text-gray-400';
    } elseif ($latest <= 500) {
        $color = '#10b981'; // green
        $dotColor = 'text-emerald-500';
    } elseif ($latest <= 1000) {
        $color = '#f59e0b'; // amber
        $dotColor = 'text-amber-500';
    } else {
        $color = '#ef4444'; // red
        $dotColor = 'text-red-500';
    }
@endphp

<div class="flex flex-col gap-y-2 px-3 py-3">
    {{-- Row 1: SSH / SSL / Uptime status icons --}}
    <div class="flex items-center gap-x-2">
        <x-filament::icon
            x-tooltip="{
                content: '{{ $record->getConnectionStatus() ? 'Connection is established.' : 'Issue with SSH connection.' }}',
                theme: $store.theme,
            }"
            icon="{{ $record->getConnectionStatus() ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle' }}"
            class="h-6 w-6 {{ $record->getConnectionStatus() ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400' }}"
        />

        <x-filament::icon
            x-tooltip="{
                content: '{{ $monitor ? ($monitor->certificate_status == 'valid' ? 'SSL is valid.' : 'No SSL found.') : 'SSL monitor not set.' }}',
                theme: $store.theme,
            }"
            icon="heroicon-m-shield-check"
            class="h-6 w-6 {{ $monitor ? ($monitor->certificate_status == 'valid' ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400') : 'text-gray-400 dark:text-gray-500' }}"
        />

        <x-filament::icon
            x-tooltip="{
                content: '{{ $monitor ? ($monitor->uptime_status == 'up' ? 'Website is up and running.' : 'Website is down.') : 'Uptime monitor not set.' }}',
                theme: $store.theme,
            }"
            icon="{{ $monitor ? ($monitor->uptime_status == 'up' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down') : 'heroicon-m-arrow-trending-up' }}"
            class="h-6 w-6 {{ $monitor ? ($monitor->uptime_status == 'up' ? 'text-green-600 dark:text-green-400' : 'text-danger-600 dark:text-danger-400') : 'text-gray-400 dark:text-gray-500' }}"
        />
    </div>

    {{-- Row 2: Response time + Sparkline --}}
    <div class="flex items-center gap-x-3">
        @if ($latest !== null)
            {{-- Response time label --}}
            <div class="flex items-center gap-x-1 min-w-[70px] justify-end">
                <span class="inline-block h-2 w-2 shrink-0 rounded-full {{ $dotColor }}"
                      style="background-color: {{ $color }};"></span>
                <span class="text-sm font-semibold tabular-nums" style="color: {{ $color }};">
                    {{ $latest }}ms
                </span>
            </div>

            {{-- Sparkline SVG --}}
            <div class="shrink-0" style="width: 100px; height: 28px;">
                @if ($logs->count() > 1)
                    @php
                        $points = $logs->toArray();
                        $count = count($points);
                        $max = max($points) ?: 1;
                        $min = min($points);
                        $range = ($max - $min) ?: 1;
                        $padMax = $max + ($range * 0.1);
                        $padMin = max(0, $min - ($range * 0.1));
                        $padRange = $padMax - $padMin;

                        $svgPoints = [];
                        foreach ($points as $i => $val) {
                            $x = round(($i / max($count - 1, 1)) * 96 + 2, 1);
                            $y = round(24 - (($val - $padMin) / $padRange) * 20 + 2, 1);
                            $svgPoints[] = "{$x},{$y}";
                        }
                        $polyline = implode(' ', $svgPoints);

                        $firstX = round(2, 1);
                        $lastX = round((($count - 1) / max($count - 1, 1)) * 96 + 2, 1);
                        $areaPath = 'M' . $firstX . ',26 L' . implode(' L', $svgPoints) . ' L' . $lastX . ',26 Z';
                    @endphp
                    <svg viewBox="0 0 100 28" preserveAspectRatio="none" class="h-full w-full">
                        {{-- Area fill --}}
                        <path d="{{ $areaPath }}"
                              fill="{{ $color }}"
                              fill-opacity="0.12" />
                        {{-- Line --}}
                        <polyline points="{{ $polyline }}"
                                  fill="none"
                                  stroke="{{ $color }}"
                                  stroke-width="1.5"
                                  stroke-linecap="round"
                                  stroke-linejoin="round" />
                    </svg>
                @endif
            </div>
        @else
            <span class="text-xs text-gray-400 dark:text-gray-500">No data</span>
        @endif
    </div>
</div>
