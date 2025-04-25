@extends('site/single/pagetemplate')

@section('content')


    <x-filament::section>
        <x-slot name="heading">
            Connected Repositories
        </x-slot>
    
        Here you can view and manage all Git repositories connected to this WordPress site. Each repository can be configured with its own deployment path and branch, and you can enable automatic deployments when changes are pushed to the repository.
    </x-filament::section>

    @livewire('list-repos', ['site_model' => $this->getRecord()])
       
@endsection
