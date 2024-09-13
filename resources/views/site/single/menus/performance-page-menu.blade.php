
<div>
    <nav class="flex flex-col text-sm sm:flex-row border-b-2 border-gray-200">

        <x-filament::tabs.item
            :href="route( 'filament.dashboard.resources.sites.performance', ['record' => $this->getRecord()->id , 'tenant' => $tenant->uuid] )" 
            tag="a"
            :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.performance', ['record' => $this->getRecord()->id , 'tenant' => $tenant->uuid ], false)"
        >
        Latest Performance
        </x-filament::tabs.item>
       
        <x-filament::tabs.item
            :href="route( 'filament.dashboard.resources.sites.performance_history', ['record' => $this->getRecord()->id , 'tenant' => $tenant->uuid] )" 
            tag="a"
            :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.performance_history', ['record' => $this->getRecord()->id , 'tenant' => $tenant->uuid ], false)"
        >
        History & Stats
        </x-filament::tabs.item>
    </nav>
</div>