<div>
    <x-filament::icon-button
        icon="heroicon-o-arrow-path"
        label="Notifications"
        {{-- wire:click="syncYourSites" --}}
        wire:click="mountAction('syncYourSites')"
        tooltip="Sync your sites"
        size="xl"
        color="gray"
    >
    </x-filament::icon-button>
    <x-filament-actions::modals />

</div>
