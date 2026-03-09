<div>
    <nav class="flex flex-col text-sm sm:flex-row border-b-2 border-gray-200">
        @php
            $tenant = Filament\Facades\Filament::getTenant(); 
        @endphp

        <x-filament::tabs.item
            :href="route( 'filament.dashboard.resources.sites.performance', ['record' => $this->getRecord()->id , 'tenant' => $tenant->uuid] )" 
            tag="a"
            :wire:navigate
            :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.performance', ['record' => $this->getRecord()->id , 'tenant' => $tenant->uuid ], false)"
        >
        Google PageSpeed
        </x-filament::tabs.item>
    </nav>
</div>
