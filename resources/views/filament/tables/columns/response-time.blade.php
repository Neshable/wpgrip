@php
    $site = $getRecord();
    $logs = $site->monitorLogs()
        ->whereNotNull('response_time_ms')
        ->where('uptime_status', 'up')
        ->latest()
        ->limit(30)
        ->pluck('response_time_ms')
        ->reverse()
        ->values();

    $latest = $logs->last();
    $avg = $logs->count() > 0 ? round($logs->avg()) : null;
    $uniqueId = 'sparkline-' . $site->id;

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

<div class="flex items-center gap-3">
    @if($latest !== null)
        {{-- Response time label --}}
        <div class="flex items-center gap-1 min-w-[70px] justify-end">
            <span class="inline-block w-2 h-2 rounded-full {{ $dotColor }}"
                  style="background-color: {{ $color }};"></span>
            <span class="text-sm font-semibold tabular-nums" style="color: {{ $color }};">
                {{ $latest }}ms
            </span>
        </div>

        {{-- Sparkline SVG --}}
        <div class="flex-shrink-0" style="width: 100px; height: 28px;">
            @if($logs->count() > 1)
                @php
                    $points = $logs->toArray();
                    $count = count($points);
                    $max = max($points) ?: 1;
                    $min = min($points);
                    // Add 10% padding
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

                    // Area fill path
                    $firstX = round(2, 1);
                    $lastX = round((($count - 1) / max($count - 1, 1)) * 96 + 2, 1);
                    $areaPath = 'M' . $firstX . ',26 L' . implode(' L', $svgPoints) . ' L' . $lastX . ',26 Z';
                @endphp
                <svg viewBox="0 0 100 28" preserveAspectRatio="none" class="w-full h-full">
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
