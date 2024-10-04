
<div>
    <nav class="flex flex-col text-sm mt-4 pb-1 sm:flex-row border-b-2 border-gray-200 dark:border-gray-900">
        @php
            $tenant = Filament\Facades\Filament::getTenant(); 
        @endphp

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
            :href="route( 'filament.dashboard.resources.sites.blacklists', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
            tag="a"
            :wire:navigate
            {{-- icon="heroicon-m-arrow-trending-up" --}}
            :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.sites.blacklists', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
        >
        Blacklist Monitor
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
        PHP Errors
            
        </x-filament::tabs.item>


    </nav>
</div>