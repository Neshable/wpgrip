@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp

<x-filament::page>
    <div class="site-page-with-sidebar">
        @include('site.single.site-sidebar')

        <div class="site-page-content">
            @yield('content')
        </div>
    </div>
</x-filament::page>
