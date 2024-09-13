@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp
    
    <x-filament::tabs class="grip-site-menu w-full" style="flex-direction: column; ">
    {{-- <x-filament::loading-indicator class="h-5 w-5" /> --}}

    {{-- <x-filament::tabs.item 
        :href="route( 'filament.dashboard.resources.sites.index', ['tenant' => $tenant->uuid] )" 
        tag="a"
        icon="heroicon-m-arrow-left">
        Back
    </x-filament::tabs.item> --}}

    <x-filament::tabs.item 
        :href="route( 'filament.dashboard.resources.sites.view', [
            'record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 
            'tenant' => $tenant->uuid
            ] )" 
        tag="a"
        {{-- icon="heroicon-m-home" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.view', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid], false)"
    >
        Overview  
    </x-filament::tabs.item>

    <x-filament::tabs.item 
        :href="route( 'filament.dashboard.resources.sites.access', [
            'record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 
            'tenant' => $tenant->uuid
            ] )" 
        tag="a"
        {{-- icon="heroicon-m-users" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.access', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid], false)"
    >
        Access  
    </x-filament::tabs.item>

    <div class="flex items-center mt-4 gap-x-3 px-3 py-2 pb-0"> 
        <span class="flex-1 text-sm  leading-6 text-gray-400 dark:text-gray-300">
            Updates
        </span>
    </div>
    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.core', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        {{-- icon="icon-wordpress" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.core', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    Core
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.plugins', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        {{-- icon="icon-plugins" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.plugins', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    Plugins
    <x-slot name="badge">
            @if ( $this->getRecord()->plugins )
                {{ count( $this->getRecord()->plugins ) }}
            @endif
        </x-slot>
    </x-filament::tabs.item>

    {{-- <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.plugins', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        icon="heroicon-m-paint-brush"
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.plugins', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    Themes

    </x-filament::tabs.item> --}}

    <div class="flex items-center mt-4 gap-x-3 px-3 py-2 pb-0"> 
        <span class="flex-1 text-sm  leading-6 text-gray-400 dark:text-gray-300">
            Performance
        </span>
    </div>


    @if ( !$this->getRecord()->is_staging )
    <x-filament::tabs.item
        :href="!$this->getRecord()->getConnectionStatus() ? '#' : route( 'filament.dashboard.resources.sites.database', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        class="{{ !$this->getRecord()->getConnectionStatus() ? 'bg-red-50' : ''}}"
        :wire:navigate
        {{-- icon="heroicon-m-circle-stack" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.database', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
       Database
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        :wire:navigate
        {{-- icon="heroicon-m-arrow-trending-up" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
       Uptime
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.blacklists', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        :wire:navigate
        {{-- icon="heroicon-m-arrow-trending-up" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.blacklists', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    Blacklists
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.performance', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        :wire:navigate
        {{-- icon="heroicon-m-presentation-chart-line" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.performance', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
       Performance
    </x-filament::tabs.item>

    <x-filament::tabs.item
    :href="route( 'filament.dashboard.resources.sites.security', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
    tag="a"
    :wire:navigate
    {{-- icon="heroicon-m-shield-check" --}}
    :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.security', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
        Security
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.errors', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        :wire:navigate
        {{-- icon="{{ $this->getRecord()->phpLogs()->count() > 0 ? 'heroicon-m-exclamation-circle' : '' }}" --}}
        icon-position="after"
        {{-- icon="heroicon-m-shield-check" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.errors', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
        Errors
         
    </x-filament::tabs.item>

    <div class="flex items-center mt-4 gap-x-3 px-3 py-2 pb-0"> 
        <span class="flex-1 text-sm  leading-6 text-gray-400 dark:text-gray-300">
            Developer
        </span>
    </div>

    <x-filament::tabs.item
        {{-- :href="route('filament.dashboard.resources.sites.backups', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid])"  --}}
        tag="a"
        {{-- icon="icon-backups" --}}
        {{-- :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.backups', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)" --}}
    >
        Backups
        <x-slot name="badge">
            @if ( $this->getRecord()->backups )
                {{ count( $this->getRecord()->backups ) }}
            @endif
        </x-slot>
    </x-filament::tabs.item>




    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        {{-- icon="heroicon-m-computer-desktop" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    Tests
    </x-filament::tabs.item>

    @if( $this->getRecord()->ssh_connection )

     

    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.staging', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        {{-- icon="heroicon-m-rectangle-stack" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.staging', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    Staging

    </x-filament::tabs.item>
    @endif
    
  
    @endif

    <x-filament::tabs.item
    :href="route( 'filament.dashboard.resources.sites.repos', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        {{-- icon="icon-git" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.repos', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    Git
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        {{-- icon="heroicon-m-wrench" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    Tools
    </x-filament::tabs.item>


</x-filament::tabs>
    



 
