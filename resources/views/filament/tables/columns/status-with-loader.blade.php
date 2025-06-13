@php
    $state = $getState();
    $colors = [
        'error' => 'danger',
        'success' => 'success', 
        'working' => 'warning',
    ];
    $color = $colors[$state] ?? 'gray';
@endphp

<div class="flex items-center gap-2">
    @if($state === 'working')
        <x-filament::loading-indicator class="text-amber-500 h-5 w-5" />
    @else
        <x-filament::badge :color="$color">
            {{ ucfirst($state) }}
        </x-filament::badge>
    @endif
</div> 