@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp


<x-filament::page>

    <div class="grid grid-cols-12 grid-rows-5 gap-4">
        <div class="col-span-2 row-span-5">
            @include('filament/menus/internal-site-menu-include')
        </div>
        <div class="col-span-10 row-span-5 col-start-3 flex flex-col gap-y-8">
            @yield('content')
        </div>
    </div>
  


</x-filament::page>