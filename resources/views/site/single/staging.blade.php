@extends('site/single/pagetemplate')

@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp

@section('content')


<x-filament::section> 
   
   <x-slot name="heading">
        Staging site
   </x-slot>

   <x-slot name="description">
       List of connected staging sites.
   </x-slot>

   <x-slot name="headerEnd">
       
   </x-slot>   

   <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4">
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
    <div class="text-gray-600">
    No staging environments have been set up for this site. To create one, please add a new site and designate this site as the primary production site. Your staging  will then be displayed here.
    </div>
    {{-- <x-filament::button wire:click="App\Filament\Resources\StagingSiteResource::CreateAction()" outlined>
        Create a staging
    </x-filament::button> --}}
    @endif
       
   </div>
   

   <x-filament-actions::modals />

   
</x-filament::section>



@endsection
