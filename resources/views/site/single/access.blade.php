@extends('site/single/pagetemplate')

@section('content')

    @include('site/single/headertemplate', [ 
        'title' => 'Access details',
        'icon' => 'heroicon-m-users' ])

    @include('site/notifications/general')

    <x-filament::section>     
        <x-slot name="heading">
            Admin Credentials
        </x-slot>
        <x-slot name="headerEnd">           
            <x-filament::link icon="heroicon-m-wrench" size="xl">
                Generate 
            </x-filament::link>
        </x-slot>
        Generate an admin account for this website or add one that already exists.
    </x-filament::section>
 

    <div class="w-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="px-6 py-4 border-b">
            <h2 class="text-2xl">
                SFTP Access
            </h2>
            <p class="text-sm text-gray-500">
                Alternative SFTP access and admin access to the website
            </p>
        </div>
        <div>
            @php
            $fields = array( 
                'Host Name' => 'n/a',
                'IP' => 'n/a',
                'SFTP User' => 'n/a',
                'SFTP Password' => 'n/a',
            );
            @endphp 

            {{-- @include('site.listing.simple', ['key' => 'Last synced', 'value' => $staging_site->last_synced ]) --}}

            @each('site.listing.simple', $fields, 'field') 
        </div>
    </div>


    
@endsection

