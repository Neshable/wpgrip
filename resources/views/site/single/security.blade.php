@extends('site/single/pagetemplate')

@section('content')

    @include('site/single/headertemplate', [ 
        'title' => 'Security',
        'icon' => 'heroicon-m-shield-check' ])

   


    <x-filament::section> 
            
    <x-slot name="heading">
        Security and vulnerability insights for your WordPress.
    </x-slot>

    n/a

    </x-filament::section>


  

    <x-filament::section>     
    <x-slot name="heading">
        3 plugins are inactive
    </x-slot>
    <x-slot name="headerEnd">           
        <x-filament::link icon="heroicon-m-wrench" size="xl" color="danger" badge-color="danger">
            Resolve <x-slot name="badge">
                3
            </x-slot>
        </x-filament::link>
    </x-slot>
    If you don't need these plugins, you should consider removing them. Deactivated plugins can still provide a way for a hacker to gain entry because the code may still be publicly accessible.
    </x-filament::section>


@endsection
