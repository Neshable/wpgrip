@extends('site/single/pagetemplate')

@section('content')

    {{-- @include('site.single.menus.security-page-submenu') --}}

    <x-filament::section> 
            
    <x-slot name="heading">
        Daily AI Insights
    </x-slot>
    <x-slot name="headerEnd">           
        <x-filament::link icon="heroicon-m-wrench" size="xl" color="info" badge-color="info">
            Get Insight 
        </x-filament::link>
    </x-slot>

    n/a

    </x-filament::section>



 


@endsection
