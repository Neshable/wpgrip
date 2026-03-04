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

{{-- Collapse the main sidebar when on a site page --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Collapse main sidebar
        if (window.Alpine && Alpine.store('sidebar')) {
            Alpine.store('sidebar').close();
        }
        // Add marker class to body
        document.body.classList.add('has-site-sidebar');
    });

    // Also handle SPA navigation (Livewire/wire:navigate)
    document.addEventListener('livewire:navigated', function() {
        // Check if we're on a site page by looking for the sub-sidebar
        const hasSiteSidebar = document.querySelector('.site-sub-sidebar');
        if (hasSiteSidebar) {
            if (window.Alpine && Alpine.store('sidebar')) {
                Alpine.store('sidebar').close();
            }
            document.body.classList.add('has-site-sidebar');
        } else {
            document.body.classList.remove('has-site-sidebar');
        }
    });
</script>
