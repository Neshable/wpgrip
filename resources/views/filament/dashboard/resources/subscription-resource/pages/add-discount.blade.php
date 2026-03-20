<x-filament-panels::page>
    <x-filament::section>
        <p class="mb-3">
            {{ __('Please enter your discount code below.') }}
        </p>

        @livewire('add-subscription-discount-form', ['subscriptionUuid' => $subscriptionUuid])

    </x-filament::section>

</x-filament-panels::page>
