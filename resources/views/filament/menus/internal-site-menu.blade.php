
<x-filament::tabs class="w-full" style="width: 100%;">
    {{-- <x-filament::loading-indicator class="h-5 w-5" /> --}}

    <x-filament::tabs.item 
        :href="route( 'filament.admin.resources.sites.index')" 
        tag="a"
        icon="heroicon-m-arrow-left">
        Back
    </x-filament::tabs.item>

    <x-filament::tabs.item 
        :href="route( 'filament.admin.resources.sites.view', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ] )" 
        tag="a"
        icon="heroicon-m-bell"
        :active="$requestUri === \URL::route('filament.admin.resources.sites.view', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
        Overview  
    </x-filament::tabs.item>

    @if ( !$this->getRecord()->is_staging )
    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        :wire:navigate
        icon="heroicon-m-bell"
        :active="$requestUri === \URL::route('filament.admin.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
       Monitoring
    </x-filament::tabs.item>

    <x-filament::tabs.item icon="heroicon-m-bell">
        Security
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route('filament.admin.resources.sites.backups', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'])" 
        tag="a"
        icon="icon-backups"
        :active="$requestUri === \URL::route('filament.admin.resources.sites.backups', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
        Backups
        <x-slot name="badge">
            @if ( $this->getRecord()->backups )
                {{ count( $this->getRecord()->backups ) }}
            @endif
        </x-slot>
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.plugins', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        icon="icon-plugins"
        :active="$requestUri === \URL::route('filament.admin.resources.sites.plugins', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
       Plugins
       <x-slot name="badge">
            @if ( $this->getRecord()->plugins )
                {{ count( $this->getRecord()->plugins ) }}
            @endif
        </x-slot>
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        icon="heroicon-m-bell"
        :active="$requestUri === \URL::route('filament.admin.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
    Tests
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.staging', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        :active="$requestUri === \URL::route('filament.admin.resources.sites.staging', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
    Staging
    </x-filament::tabs.item>
    @endif
    
    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.repos', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        icon="icon-git"
        :active="$requestUri === \URL::route('filament.admin.resources.sites.repos', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
    Git
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        :active="$requestUri === \URL::route('filament.admin.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
    Tools
    </x-filament::tabs.item>


</x-filament::tabs>
