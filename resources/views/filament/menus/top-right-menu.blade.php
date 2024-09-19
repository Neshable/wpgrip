  
<x-filament::modal>

    <x-slot name="trigger">
        <x-filament::icon-button
            icon="heroicon-o-key"
            label="SSH Key"
            tooltip="Get API Key"
            color="info"
            size="xl"
        />
    </x-slot>

    @php
        $tenant = Filament\Facades\Filament::getTenant();
    @endphp


    This is your public SSH key. Please add it to the authorized keys on your server to ensure secure access to the site.
                
    <x-filament::input.wrapper class="mt-3" disabled>
        <x-filament::input
            type="text"
            label="asd"
            wire:model="name"
            value="{{ $tenant ? $tenant->getPublicKey() : '' }}"
            disabled
        />
    </x-filament::input.wrapper>

    {{--   --}}

    <x-filament::link
    icon="heroicon-m-clipboard" 
    href="#"
    color="info"
    x-on:click="
    window.navigator.clipboard.writeText('{{ $tenant ? $tenant->getPublicKey() : '' }}')
    $tooltip('Key copied to clipboard', { timeout: 2000 })" >
        Copy key
    </x-filament::link>

</x-filament::modal>

@livewire('notifications')

{{-- <div class="hidden sm:flex">

<x-filament::icon-button
    alias="panels::topbar.open-database-notifications-button"
    icon="heroicon-o-question-mark-circle"
    label="Notifications"
    color="gray"
    size="xl"
>
</x-filament::icon-button>
</div> --}}

{{-- <x-filament::badge size="sm">
    New
</x-filament::badge> --}}

{{-- <x-filament::modal  

    slide-over>

    <x-slot name="trigger">
        <x-filament::icon-button
            icon="heroicon-o-user-group"
            label="Workspace"
        >

        </x-filament::icon-button>
    </x-slot>

    <x-slot name="heading">
        Choose your workspace
    </x-slot>

    @if (filament()->hasTenancy())
        <x-filament-panels::tenant-menu class="block" />
    @endif

</x-filament::modal> --}}

