@extends('site/single/pagetemplate')

@section('content')

@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp


    
    @livewire(\App\Filament\Dashboard\Resources\SiteResource\Widgets\SitePerformanceHistory::class, [ 'type' => 'desktop' ])

    @livewire(\App\Filament\Dashboard\Resources\SiteResource\Widgets\SitePerformanceHistory::class, [ 'type' => 'mobile' ])

    @livewire(\App\Filament\Dashboard\Resources\SiteResource\Widgets\DomSizeChart::class) 

    {{-- @livewire(\App\Filament\App\Resources\SiteResource\Widgets\SiteMetricsChart::class)  --}}
 
@endsection
