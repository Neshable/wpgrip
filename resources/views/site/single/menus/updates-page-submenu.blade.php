
<div>
    <nav class="flex flex-col text-sm mt-4 pb-1 sm:flex-row border-b-2 border-gray-200 dark:border-gray-900">
        @php
            $tenant = Filament\Facades\Filament::getTenant(); 
        @endphp

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

        <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.themes', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        {{-- icon="icon-wordpress" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.themes', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
        Themes
        <x-slot name="badge">
            @if ( $this->getRecord()->themes )
                {{ count( $this->getRecord()->themes ) }}
            @endif
        </x-slot>
        </x-filament::tabs.item>

        <x-filament::tabs.item
        :href="route( 'filament.dashboard.resources.sites.core', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
        tag="a"
        {{-- icon="icon-wordpress" --}}
        :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.core', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
        Core
        </x-filament::tabs.item>


    </nav>
</div>