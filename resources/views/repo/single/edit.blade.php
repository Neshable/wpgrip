@extends('repo/single/pagetemplate')

@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp

@section('content')

<x-filament::section>
    <x-slot name="heading">
        Repository setting
    </x-slot>
 
    <x-slot name="description">
        You can change the path or other details about the repository. 
    </x-slot>

    <x-slot name="headerEnd">
        {{-- Input to select the user's ID --}}
    </x-slot>
 
    <form  wire:submit="save">
        {{ $this->form }}

        <x-filament::button class="my-4" type="submit" size="lg">
            Save settings
        </x-filament::button>

          
        <x-filament::button
            :href="route( 'filament.dashboard.resources.repositories.view', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
            tag="a"
            color="gray"
            size="lg"
            class="my-4"
            {{-- icon="heroicon-m-cog-6-tooth" --}}
        >
        Return to repository
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
    
</x-filament::section>




    
@endsection

