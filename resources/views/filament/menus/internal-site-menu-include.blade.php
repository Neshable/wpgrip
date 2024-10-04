@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp
    
<x-filament::tabs class="w-full">
   
<x-filament::tabs.item 
    :href="route( 'filament.dashboard.resources.sites.view', [
        'record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 
        'tenant' => $tenant->uuid
        ] )" 
    :wire:navigate
    tag="a"
    icon="heroicon-m-home"
    :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.view', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid], false)"
>
    Overview  
</x-filament::tabs.item>


<x-filament::tabs.item
    :href="route( 'filament.dashboard.resources.sites.plugins', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
    tag="a"
    :wire:navigate
    icon="icon-plugins"
    :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.plugins', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
>
Updates
</x-filament::tabs.item>


<x-filament::tabs.item
    :href="route( 'filament.dashboard.resources.sites.performance', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
    tag="a"
    :wire:navigate
    icon="heroicon-m-presentation-chart-line"
    :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.performance', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
>
Performance
</x-filament::tabs.item>



<x-filament::tabs.item
    :href="route( 'filament.dashboard.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
    tag="a"
    :wire:navigate
    icon="heroicon-m-arrow-trending-up"
    :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
>
Monitors
</x-filament::tabs.item>

<x-filament::tabs.item
    :href="route( 'filament.dashboard.resources.sites.security', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
    tag="a"
    :wire:navigate
    icon="heroicon-m-shield-check"
    :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.security', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    Security
</x-filament::tabs.item>


<x-filament::tabs.item
    :href="route( 'filament.dashboard.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
    tag="a"
    :wire:navigate
    icon="heroicon-m-wrench"
    :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
>
Dev Tools
</x-filament::tabs.item>

{{-- Other tabs --}}
</x-filament::tabs>
    



 
