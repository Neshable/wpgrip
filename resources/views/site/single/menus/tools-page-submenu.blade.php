
<div>
    <nav class="flex flex-col text-sm mt-4 pb-1 sm:flex-row border-b-2 border-gray-200 dark:border-gray-900">
        @php
            $tenant = Filament\Facades\Filament::getTenant(); 
        @endphp
    <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        {{-- icon="heroicon-m-wrench" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.tools', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
    >
    All Tools
    </x-filament::tabs.item>

        <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.repos', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
            tag="a"
            {{-- icon="icon-git" --}}
            :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.repos', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
        Git
        </x-filament::tabs.item>

        
    <x-filament::tabs.item
        {{-- :href="route( 'filament.dashboard.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )"  --}}
        tag="a"
        {{-- icon="heroicon-m-computer-desktop" --}}
        {{-- :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.tests', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)" --}}
    >
    Tests
    </x-filament::tabs.item>

        <x-filament::tabs.item
            :href="route( 'filament.dashboard.resources.sites.staging', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
            tag="a"
            {{-- icon="heroicon-m-rectangle-stack" --}}
            :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.staging', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
        Staging

        </x-filament::tabs.item>

        <x-filament::tabs.item
            :href="route('filament.dashboard.resources.sites.backups', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid])" 
            tag="a"
            {{-- icon="icon-backups" --}}
            :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.backups', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
            Backups
            <x-slot name="badge">
                @if ( $this->getRecord()->backups )
                    {{ count( $this->getRecord()->backups ) }}
                @endif
            </x-slot>
        </x-filament::tabs.item>
    </nav>
</div>