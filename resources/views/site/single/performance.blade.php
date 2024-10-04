@extends('site/single/pagetemplate')

@section('content')

@php
    $tenant = Filament\Facades\Filament::getTenant(); 

    $desktop = \App\Models\PerformanceData::where('site_id', $this->record->id )
    ->where('strategy', 'desktop')
    ->orderBy('created_at', 'desc')
    ->first();

    $mobile = \App\Models\PerformanceData::where('site_id', $this->record->id )
    ->where('strategy', 'mobile')
    ->orderBy('created_at', 'desc')
    ->first();

@endphp

    @include('site.single.menus.performance-page-menu')

    <x-filament::section> 
        
        <x-slot name="heading">
            Latest desktop performance score
        </x-slot>

        <x-slot name="headerEnd">
            @if ( $desktop )
            <div>{{ $desktop->created_at ? $desktop->created_at->diffForHumans() : 'n/a' }}</div>
            @endif
            {{-- <x-filament::button wire:click="runLightHouseTest" outlined>
                Run desktop test
            </x-filament::button> --}}
        </x-slot>   
        
      

        @if ( $desktop )
        <div>
            <div class="flex flex-wrap md:flex-nowrap mb-8">
                
                <div class="w-full md:w-1/2 flex flex-col justify-center space-y-4">
                    <div class="mb-4">
                        <p class="text-sm">Values are estimated and may vary. The performance score is calculated directly from these metrics. <a href="#" class=" text-blue-600">See calculator.</a></p>
                    </div>
            
                    <div class="flex justify-center mb-8">
                        <div class="w-full">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-red-600">0–49</div>
                                <div class="text-orange-600">50–89</div>
                                <div class="text-green-500">90–100</div>
                            </div>
                            <div class="bg-gray-200 h-2 rounded-full">
                                <div class="{{ \App\Services\Helpers\BladeHelper::getPerformanceBackgroundClass( $desktop->performance ?? null, true ) }} h-2 rounded-full" style="width: {{ $desktop->performance ?? 99}}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2 mb-8 md:mb-0">
                    <div class="flex justify-center">
                        <div class="w-32 h-32 {{ \App\Services\Helpers\BladeHelper::getPerformanceBackgroundClass( $desktop->performance ?? null ) }} rounded-full flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-6xl font-bold {{ \App\Services\Helpers\BladeHelper::getPerformanceTextClass( $desktop->performance ?? null ) }}">{{ $desktop->performance }}</div>
                                <div class="text-lg font-semibold">Desktop</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            
    
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">METRICS</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-100 bg-orange-400 rounded-full mr-2"></div>
                            <div>First Contentful Paint</div>
                        </div>
                        <div class="text-red-600">{{ $desktop->fcp !== null ? $desktop->fcp / 1000 : 'N/A' }}s</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-100 bg-red-600 rounded-full mr-2"></div>
                            <div>Speed Index</div>
                        </div>
                        <div class="text-red-600">{{ $desktop->speed_index !== null ? $desktop->speed_index / 1000 : 'N/A' }}s</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-100  bg-green-500 rounded-full mr-2"></div>
                            <div>Total Blocking Time</div>
                        </div>
                        <div class="text-green-500">{{ $desktop->total_blocking_time !== null ? $desktop->total_blocking_time / 1000 : 'N/A' }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-400 rounded-full mr-2"></div>
                            <div>Largest Contentful Paint</div>
                        </div>
                        <div class="text-red-600">{{ $desktop->lcp !== null ? $desktop->lcp / 1000 : 'N/A' }} s</div>
                    </div>
     
                </div>
            </div>
    
            <div class="text-xs text-gray-500">
                <div class="flex items-center justify-between mb-1">
                    <div>Captured at {{ $desktop->created_at ?? 'n/a' }}</div>
                    <div>Lighthouse 11.0.0</div>
                </div>
        
                <div class="flex items-center justify-between">
                    <div>Using HeadlessChromium 119.0.0.645.123 with lr</div>
                </div>
            </div>
        </div>
        @endif
     

    </x-filament::section>

    <x-filament::section> 
        
  
    @if ( $mobile )
    <x-slot name="heading">
        Latest mobile performance score
    </x-slot>

    <x-slot name="headerEnd">
        <div>{{ $mobile->created_at ? $mobile->created_at->diffForHumans() : 'n/a' }}</div>
        {{-- <x-filament::button wire:click="runLightHouseTest" outlined>
            Run mobile test
        </x-filament::button> --}}
    </x-slot>   
    
    <div>
        <div class="flex flex-wrap md:flex-nowrap mb-8">
            
            <div class="w-full md:w-1/2 flex flex-col justify-center space-y-4">
                <div class="mb-4">
                    <p class="text-sm">Values are estimated and may vary. The performance score is calculated directly from these metrics. <a href="#" class=" text-blue-600">See calculator.</a></p>
                </div>
        
                <div class="flex justify-center mb-8">
                    <div class="w-full">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-red-600">0–49</div>
                            <div class="text-orange-600">50–89</div>
                            <div class="text-green-500">90–100</div>
                        </div>
                        <div class="bg-gray-200 h-2 rounded-full">
                            <div class="{{ \App\Services\Helpers\BladeHelper::getPerformanceBackgroundClass( $mobile->performance ?? null, true ) }} h-2 rounded-full" style="width: {{ $mobile->performance ?? 99}}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full md:w-1/2 mb-8 md:mb-0">
                <div class="flex justify-center">
                    <div class="w-32 h-32 {{ \App\Services\Helpers\BladeHelper::getPerformanceBackgroundClass( $mobile->performance ?? null ) }} rounded-full flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-6xl font-bold {{ \App\Services\Helpers\BladeHelper::getPerformanceTextClass( $mobile->performance ?? null ) }}">{{ $mobile->performance }}</div>
                            <div class="text-lg font-semibold">Mobile</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">METRICS</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-orange-100 bg-orange-400 rounded-full mr-2"></div>
                        <div>First Contentful Paint</div>
                    </div>
                    <div class="text-red-600">{{ $mobile->fcp !== null ? $mobile->fcp / 1000 : 'N/A' }}s</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-100 bg-red-600 rounded-full mr-2"></div>
                        <div>Speed Index</div>
                    </div>
                    <div class="text-red-600">{{ $mobile->speed_index !== null ? $mobile->speed_index / 1000 : 'N/A' }} s</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-100  bg-green-500 rounded-full mr-2"></div>
                        <div>Total Blocking Time</div>
                    </div>
                    <div class="text-green-500">{{ $mobile->total_blocking_time !== null ? $mobile->total_blocking_time / 1000 : 'N/A' }}</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-orange-400 rounded-full mr-2"></div>
                        <div>Largest Contentful Paint</div>
                    </div>
                    <div class="text-red-600">{{ $mobile->lcp !== null ? $mobile->lcp / 1000 : 'N/A' }}s</div>
                </div>
 
            </div>
        </div>

        <div class="text-xs text-gray-500">
            <div class="flex items-center justify-between mb-1">
                <div>Captured at {{ $mobile->created_at ?? 'n/a' }}</div>
                <div>Lighthouse 11.0.0</div>
            </div>
    
            <div class="flex items-center justify-between">
                <div>Using Headless Chromium 119.0.0.645.123 with lr</div>
            </div>
        </div>
    </div>
    @endif
 

</x-filament::section>
 
@endsection
