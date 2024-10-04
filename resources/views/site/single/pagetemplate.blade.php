@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp


<x-filament::page>

    @include('filament/menus/internal-site-menu-include')

    <div class="grid grid-cols-12 grid-rows-5 gap-4">
        <div class="col-span-12 row-span-5 col-start-1 flex flex-col gap-y-8">
            @yield('content')
        </div>
    </div>

</x-filament::page>