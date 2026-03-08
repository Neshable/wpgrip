<header
    class="fixed inset-x-0 top-0 z-50 border-b transition-all duration-300 backdrop-blur-md"
    x-data="{
        scrolled: window.pageYOffset >= 24,
        mobileOpen: false,
        init() { this.evaluate() },
        evaluate() { this.scrolled = window.pageYOffset >= 24 },
    }"
    x-on:scroll.window.passive="evaluate"
    :class="scrolled
        ? 'border-white/10 bg-neutral-950/90 shadow-[0_4px_24px_rgba(0,0,0,0.5)]'
        : 'border-white/[0.06] bg-neutral-950/70'"
>

    <div class="max-w-screen-xl mx-auto px-6 flex items-center justify-between h-16">
        {{-- Logo --}}
        <a href="/" class="flex items-center gap-3">
            <img src="{{ asset(config('app.logo.dark')) }}" class="h-8" alt="{{ config('app.name') }}" />
        </a>

        {{-- Desktop Nav Center --}}
        <nav class="hidden md:flex items-center gap-8">
            <a href="{{ route('features') }}" class="text-sm font-light text-neutral-400 hover:text-white transition-colors duration-200">Features</a>
            <a href="{{ route('pricing') }}" class="text-sm font-light text-neutral-400 hover:text-white transition-colors duration-200">Pricing</a>
            <a href="{{ route('why-ssh') }}" class="text-sm font-light text-neutral-400 hover:text-white transition-colors duration-200">Why SSH</a>
            <a href="{{ config('app.docs_url', '/docs') }}" class="text-sm font-light text-neutral-400 hover:text-white transition-colors duration-200">Docs</a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-white hover:text-blue-400 transition-colors duration-200">Dashboard</a>
            @endauth
        </nav>

        {{-- Desktop Nav End --}}
        <div class="hidden md:flex items-center gap-2">
            @auth
                <x-layouts.app.user-menu />
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center h-9 px-4 rounded-md border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-colors">
                    Sign in
                </a>
            @endauth
        </div>

        {{-- Mobile hamburger --}}
        <button class="md:hidden text-neutral-400 hover:text-white p-2" @click="mobileOpen = !mobileOpen" aria-label="Toggle menu">
            <svg x-show="!mobileOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen" x-cloak x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="md:hidden border-t border-white/10 bg-neutral-950/95 backdrop-blur-md">
        <nav class="flex flex-col p-4 gap-1">
            <a href="{{ route('features') }}" class="px-3 py-3 text-neutral-400 hover:text-white text-sm">Features</a>
            <a href="{{ route('pricing') }}" class="px-3 py-3 text-neutral-400 hover:text-white text-sm">Pricing</a>
            <a href="{{ route('why-ssh') }}" class="px-3 py-3 text-neutral-400 hover:text-white text-sm">Why SSH</a>
            <a href="{{ config('app.docs_url', '/docs') }}" class="px-3 py-3 text-neutral-400 hover:text-white text-sm">Docs</a>
            @auth
                <a href="{{ route('dashboard') }}" class="px-3 py-3 text-white text-sm font-medium">Dashboard</a>
            @else
                <div class="mt-4 flex flex-col gap-3 pt-4 border-t border-white/10">
                    <a href="{{ route('login') }}" class="inline-flex justify-center items-center h-10 rounded-md border border-white/10 bg-white/5 text-neutral-300 text-sm">Sign in</a>
                </div>
            @endauth
        </nav>
    </div>
</header>
