@extends('site/single/pagetemplate')

@section('content')

@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp

    <div>
    @include('site/single/headertemplate', [ 
            'title' => 'Performance',
            'icon' => 'heroicon-m-presentation-chart-line' ])

    @include('site.single.menus.performance-page-menu')
    </div>

    
    @livewire(\App\Filament\App\Resources\SiteResource\Widgets\SitePerformanceChart::class, [
        'type' => 'lighthouse',
        'record' => $this->getRecord()
        ])

     @livewire(\App\Filament\App\Resources\SiteResource\Widgets\ResponseTimeChart::class) 

    @livewire(\App\Filament\App\Resources\SiteResource\Widgets\SiteMetricsChart::class) 
 
@endsection
