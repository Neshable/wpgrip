@extends('site/single/pagetemplate')

@section('content')

    @include('site.single.menus.tools-page-submenu')

    {{-- <x-filament::section>
        <x-slot name="heading">
            Connected Repositories
        </x-slot>
    
        Your Git repository should support Git over SSH. An SSH Key identifies your server without the need of passwords. You will first need to generate and download an SSH Key. It's very easy. Just click the button below.
    </x-filament::section> --}}

    @livewire('list-repos', ['site_model' => $this->getRecord() ] )
       
@endsection
