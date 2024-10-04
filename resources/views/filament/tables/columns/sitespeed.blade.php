@if ( !$getRecord()->is_staging )
@php
    $desktop = \App\Models\PerformanceData::where('site_id',$getRecord()->id )
    ->where('strategy', 'desktop')
    ->orderBy('created_at', 'desc')
    ->first();

    $mobile = \App\Models\PerformanceData::where('site_id', $getRecord()->id )
    ->where('strategy', 'mobile')
    ->orderBy('created_at', 'desc')
    ->first();

@endphp

<div class="fi-ta-text grid gap-y-1 py-1">    
    <div class="{{ \App\Services\Helpers\BladeHelper::getPerformanceBackgroundClass( $desktop->performance ?? null ) }} group relative inline-flex items-center gap-2 cursor-pointer text-sm transition-all rounded-full px-2 py-1">
        <div class="rounded-full {{ \App\Services\Helpers\BladeHelper::getPerformanceTextClass( $desktop->performance ?? null ) }}">
            <x-filament::icon
                icon="heroicon-m-computer-desktop"
                class="h-4 w-5 {{ \App\Services\Helpers\BladeHelper::getPerformanceTextClass( $desktop->performance ?? null ) }}" />
        </div>
        <span class="text-xs {{ \App\Services\Helpers\BladeHelper::getPerformanceTextClass( $desktop->performance ?? null ) }} font-bold">{{ $desktop->performance ?? '-' }}/100</span>
    </div>
    <div class="{{ \App\Services\Helpers\BladeHelper::getPerformanceBackgroundClass( $mobile->performance ?? null ) }} group relative inline-flex items-center gap-2 cursor-pointer text-sm transition-all rounded-full px-2 py-1">
        <div class="rounded-full {{ \App\Services\Helpers\BladeHelper::getPerformanceTextClass( $mobile->performance ?? null ) }}">
            <x-filament::icon
                icon="heroicon-m-device-phone-mobile"
                class="h-4 w-5 {{ \App\Services\Helpers\BladeHelper::getPerformanceTextClass( $mobile->performance ?? null ) }}" />        
        </div>
        <span class="text-xs {{ \App\Services\Helpers\BladeHelper::getPerformanceTextClass( $mobile->performance ?? null ) }} font-bold">{{ $mobile->performance ?? '-' }}/100</span>
    </div> 
</div>

@else

<div class="fi-ta-text grid gap-y-1 py-1">    
    <div class=" group relative inline-flex items-center gap-2 cursor-pointer text-sm transition-all rounded-full pr-2">
        <div class="rounded-full bg-white">
            <x-filament::icon
                icon="heroicon-m-computer-desktop"
                class="h-5 w-5 text-gray-500 dark:text-gray-400" />
        </div>
        <span class="text-xs  font-bold">Disabled</span>
    </div>
    <div class="group relative inline-flex items-center gap-2 cursor-pointer text-sm transition-all rounded-full pr-2">
        <div class="rounded-full bg-white">
            <x-filament::icon
                icon="heroicon-m-device-phone-mobile"
                class="h-5 w-5 text-gray-500 dark:text-gray-400" />        
        </div>
        <span class="text-xs font-bold">Disabled</span>
    </div> 
</div>

@endif