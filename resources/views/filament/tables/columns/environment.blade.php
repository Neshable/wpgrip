@if ( $getRecord()->is_staging )
    <x-filament::badge color="gray">
        Staging
    </x-filament::badge>
@else
    <x-filament::badge color="info">
        Production
    </x-filament::badge>
@endif