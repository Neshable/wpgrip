@extends('site/single/pagetemplate')

@section('content')
    
@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp

    @include('site/single/headertemplate', [ 
        'title' => 'PHP Errors',
        'icon' => 'heroicon-m-computer-desktop' ])


    {{-- @include('site.single.menus.test-page-menu') --}}

    <x-filament::section>
        <x-slot name="heading">
           Error log file location
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::button wire:click="mountAction('editLogLocationAction')" outlined>
                Change
            </x-filament::button>
        </x-slot>

       

        The path for the PHP error logs differs across various servers. Please input your absolute path to the file.
        
        <x-filament::input.wrapper class="mt-3 rounded-lg shadow-sm ring-1 transition duration-75  dark:bg-white/5 focus-within:ring-primary-600 dark:ring-white/20 dark:focus-within:ring-primary-500" disabled>
            <x-filament::input
                type="text"
                label="Log path"
                wire:model="name"
                value="{{ $this->getRecord()->error_log_path ? $this->getRecord()->error_log_path : 'No path specified'}}"
                disabled
            />
        </x-filament::input.wrapper>

    </x-filament::section>

    @livewire('list-errors', ['site_id' => $this->getRecord()->id ] )


@endsection
