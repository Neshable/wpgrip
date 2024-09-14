
@php
    $monitor = $this->getRecord()->get_main_monitor();   
@endphp

@extends('site/single/pagetemplate')

@section('content')
    <div>
    @include('site/single/headertemplate', [ 
        'title' => 'Monitors',
        'icon' => 'heroicon-m-arrow-trending-up' ])

    @include('site.single.menus.monitor-page-menu')
    </div>
        
    @if (  $monitor )

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



    @endif
@endsection
