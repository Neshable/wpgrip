@props(['backButton' => true])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.partials.head')
</head>
<body {{ $attributes->merge(['class' => 'bg-neutral-950 text-neutral-100 w-full min-h-screen']) }}>

    {{-- dot-grid + blue glow background --}}
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute inset-0" style="background-image:radial-gradient(circle,rgba(255,255,255,0.10) 1px,transparent 1px);background-size:28px 28px;"></div>
        <div class="absolute inset-0" style="background:radial-gradient(ellipse 100% 60% at 50% 0%,rgba(37,99,235,0.22) 0%,transparent 70%);"></div>
        <svg class="absolute inset-0 w-full h-full opacity-[0.05]" xmlns="http://www.w3.org/2000/svg"><filter id="n"><feTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch"/><feColorMatrix type="saturate" values="0"/></filter><rect width="100%" height="100%" filter="url(#n)"/></svg>
    </div>

    <div id="app" class="relative">

        {{-- top bar: logo + optional back link --}}
        <div class="flex items-center justify-between px-6 py-5">
            <a href="{{ route('home') }}">
                <img src="{{ asset(config('app.logo.dark')) }}" class="h-8" alt="{{ config('app.name') }}" />
            </a>

            @if($backButton)
                <a href="{{ route('home') }}" class="text-xs text-neutral-400 hover:text-neutral-200 transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </a>
            @endif
        </div>

        <div>
            {{ $slot }}
        </div>

        @include('components.layouts.partials.tail', ['skipCookieContentBar' => true])
    </div>
</body>
</html>
