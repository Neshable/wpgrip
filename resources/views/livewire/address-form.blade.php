<x-filament::section>
    <x-slot name="heading">{{ __('Address') }}</x-slot>
    <x-slot name="description">{{ __('Enter your address details.') }}</x-slot>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}
        <div class="text-right">
            <x-filament::button type="submit" form="submit" class="align-right">
                {{ __('Save') }}
            </x-filament::button>
        </div>
    </form>
</x-filament::section>
