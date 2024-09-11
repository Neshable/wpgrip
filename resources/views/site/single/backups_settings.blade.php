@extends('site/single/pagetemplate')

@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp

@section('content')

@include('site/single/headertemplate', [ 
        'title' => 'Backups Settings',
        'icon' => 'icon-backups' ])

<x-filament::section>
    <x-slot name="heading">
        Backups
    </x-slot>
 
    <x-slot name="description">
        You can exclude/include files, as well as change the schedule.
    </x-slot>

    <x-slot name="headerEnd">
        {{-- Input to select the user's ID --}}
    </x-slot>
 
    <form  wire:submit="save">
        {{ $this->form }}

        <x-filament::button class="my-4" type="submit" size="lg">
            Save settings
        </x-filament::button>

        
    </form>

    <x-filament-actions::modals />
    
</x-filament::section>




    
@endsection

