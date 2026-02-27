@props(['backButton' => true])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full bg-neutral-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.partials.head')
</head>
<body class="bg-neutral-950 text-neutral-300 min-h-screen antialiased" {{ $attributes }}>

    {{-- Noise texture overlay (same as app.blade.php) --}}
    <svg class="pointer-events-none fixed inset-0 z-[99] h-screen mix-blend-overlay" style="opacity:0.06" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" preserveAspectRatio="none">
        <defs>
            <filter id="noise-filter">
                <feTurbulence type="turbulence" baseFrequency="0.85" numOctaves="1" stitchTiles="stitch" result="noise" />
                <feColorMatrix type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 1 0" result="coloredNoise" />
            </filter>
        </defs>
        <rect width="100%" height="100%" filter="url(#noise-filter)" />
    </svg>

    {{-- Dot grid --}}
    <div class="pointer-events-none fixed inset-0" style="background-image:radial-gradient(circle,rgba(255,255,255,0.12) 1px,transparent 1px);background-size:28px 28px;"></div>

    {{-- Blue radial glow --}}
    <div class="pointer-events-none fixed inset-0" style="background:radial-gradient(ellipse 90% 65% at 50% -10%,rgba(37,99,235,0.35) 0%,rgba(37,99,235,0.10) 40%,transparent 70%);"></div>

    {{-- Purple secondary tint --}}
    <div class="pointer-events-none fixed inset-0" style="background:radial-gradient(ellipse 50% 50% at 80% 20%,rgba(124,58,237,0.12) 0%,transparent 60%);"></div>

    <div id="app" class="relative">

        {{-- top bar --}}
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

        <div>{{ $slot }}</div>

        @include('components.layouts.partials.tail', ['skipCookieContentBar' => true])
    </div>
</body>
</html>
