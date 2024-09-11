
<div>
    <nav class="flex flex-col text-sm sm:flex-row border-b-2 border-gray-200">
        {{-- <button class="text-gray-600 mr-6 py-4  block hover:text-primary-600 focus:outline-none text-primary-600 border-b-2 font-medium border-blue-500">
            Visual Tests
        </button> --}}

        <x-filament::tabs.item
            :href="route( 'filament.app.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->slug] )" 
            tag="a"
            :active="request()->getRequestUri() === \URL::route('filament.app.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->slug ], false)"
        >
        Visual Tests
        </x-filament::tabs.item>
        
        <x-filament::tabs.item
            :href="route( 'filament.app.resources.sites.performance', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->slug] )" 
            tag="a"
            :active="request()->getRequestUri() === \URL::route('filament.app.resources.sites.performance', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->slug ], false)"
        >
        Performance
        </x-filament::tabs.item>
    </nav>
</div>