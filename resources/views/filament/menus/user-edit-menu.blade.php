
    
    <x-filament::tabs class="w-full" style="flex-direction: column; ">

    <x-filament::tabs.item 
        href="#" 
        tag="a"
        icon="heroicon-m-home"
    >
        Overview  
    </x-filament::tabs.item>

    {{-- @if ( !$this->getRecord()->is_staging )
    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        :wire:navigate
        icon="heroicon-m-presentation-chart-line"
        :active="request()->getRequestUri() === \URL::route('filament.admin.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
       Monitoring
    </x-filament::tabs.item>

    <x-filament::tabs.item
    :href="route( 'filament.admin.resources.sites.security', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
    tag="a"
    :wire:navigate
    icon="heroicon-m-shield-check"
    :active="request()->getRequestUri() === \URL::route('filament.admin.resources.sites.security', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
        >
        Security
        </x-filament::tabs.item>



    <x-filament::tabs.item
        :href="route('filament.admin.resources.sites.backups', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'])" 
        tag="a"
        icon="icon-backups"
        :active="request()->getRequestUri() === \URL::route('filament.admin.resources.sites.backups', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
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
        :active="request()->getRequestUri() === \URL::route('filament.admin.resources.sites.plugins', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
       Plugins
       <x-slot name="badge">
            @if ( $this->getRecord()->plugins )
                {{ count( $this->getRecord()->plugins ) }}
            @endif
        </x-slot>
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.performance', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        icon="heroicon-m-computer-desktop"
        :active="request()->getRequestUri() === \URL::route('filament.admin.resources.sites.performance', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
    Performance
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        icon="heroicon-m-computer-desktop"
        :active="request()->getRequestUri() === \URL::route('filament.admin.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
    Tests
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.staging', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        icon="heroicon-m-rectangle-stack"
        :active="request()->getRequestUri() === \URL::route('filament.admin.resources.sites.staging', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
    Staging
    @if( $this->getRecord()->children() ) 
        <x-slot name="badge">
            1
        </x-slot>
    @endif

    </x-filament::tabs.item>
    @endif
    
    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.repos', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        icon="icon-git"
        :active="request()->getRequestUri() === \URL::route('filament.admin.resources.sites.repos', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
    Git
    </x-filament::tabs.item>

    <x-filament::tabs.item
        :href="route( 'filament.admin.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2'] )" 
        tag="a"
        icon="heroicon-m-wrench"
        :active="request()->getRequestUri() === \URL::route('filament.admin.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2' ], false)"
    >
    Tools
    </x-filament::tabs.item> --}}


</x-filament::tabs>
    



 
