
<div>
    <nav class="flex flex-col text-sm mt-4 pb-1 sm:flex-row border-b-2 border-gray-200 dark:border-gray-900">
        @php
            $tenant = Filament\Facades\Filament::getTenant(); 
        @endphp
        <x-filament::tabs.item
            :href="route( 'filament.dashboard.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
            tag="a"
            :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.monitoring', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
        Main monitor
        </x-filament::tabs.item>
        
        <x-filament::tabs.item
            :href="route( 'filament.dashboard.resources.sites.monitors', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
            tag="a"
            :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.monitors', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
        Additional monitors
        </x-filament::tabs.item>
    </nav>
</div>