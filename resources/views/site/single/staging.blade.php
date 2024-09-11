@extends('site/single/pagetemplate')

@php
$tenant = Filament\Facades\Filament::getTenant(); 

@endphp

@section('content')

    @include('site/single/headertemplate', [ 
                'title' => 'Staging',
                'icon' => 'heroicon-m-rectangle-stack' ])

    <x-filament::fieldset class="bg-white  dark:bg-gray-900">
        <x-slot name="label">
            Important
        </x-slot>
        
        {{-- In order to successfully sync the live to staging, a SSH connection between the two servers must be established. The LIVE server public key needs to be added to the STAGING server. --}}

    </x-filament::fieldset>


    <div class="w-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        
        <div class="px-6 py-4 border-b flex flex-col gap-3 overflow-hidden sm:flex-row sm:items-center">

            <h2 class="text-2xl grid flex-1 gap-y-1">
                Staging site
            </h2> 
        
            <x-filament::button wire:click="mountAction('syncLiveToStaging')" outlined>
                Sync Staging
            </x-filament::button> 
        </div>
        @if ( $staging_site = $this->getStagingSite() )
        <div>
            @php
           $fields = array( 
                    'Last synced' => $staging_site->last_sync,
                    'Staging URL' => $staging_site->url,
                    'SSH User' => $staging_site->ssh_user,
                    'Dir path' => $staging_site->dir_path,
                    'Status' => $staging_site->status,
                    'Server Name' => $staging_site->server->name,
                    'Server IP' => $staging_site->server->ip,
                );
            @endphp 
        
            {{-- @include('site.listing.simple', ['key' => 'Last synced', 'value' => $staging_site->last_synced ]) --}}
        
            @each('site.listing.simple', $fields, 'field') 
        </div>
        @endif
        @if ( !$staging_site = $this->getStagingSite() )
           
        No staging environments have been set up for this site. To create one, please add a new site and designate this site as the primary production site. Your staging  will then be displayed here.

        {{-- <x-filament::button wire:click="App\Filament\Resources\StagingSiteResource::CreateAction()" outlined>
            Create a staging
        </x-filament::button> --}}
        @endif
    </div>

@endsection
