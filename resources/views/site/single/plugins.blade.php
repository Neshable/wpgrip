@extends('site/single/pagetemplate')

@section('content')

    @include('site.single.menus.updates-page-submenu')

    @if ( $this->getRecord()->is_staging )

    <x-filament::section> 
        
        <x-slot name="heading">
            Feature not active
        </x-slot>

        <x-slot name="headerEnd">
            Staging detected
        </x-slot>   

        One or more features are not available in the staging mode. This website is detected to be a staging.

    </x-filament::section>
    @elseif( !$this->getRecord()->ssh_connection )
        @include('site/notifications/general')
    @else
   

    @livewire('list-plugins', ['site_model' => $this->getRecord() ] )

    @endif

@endsection

