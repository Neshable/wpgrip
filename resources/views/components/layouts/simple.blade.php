<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full bg-neutral-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.partials.head')
</head>
<body class="bg-neutral-950 text-neutral-300 antialiased" x-data>

    {{-- Noise texture overlay --}}
    <svg class="pointer-events-none fixed inset-0 z-[99] h-screen mix-blend-overlay" style="opacity:0.06" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" preserveAspectRatio="none">
        <defs>
            <filter id="noise-filter">
                <feTurbulence type="turbulence" baseFrequency="0.85" numOctaves="1" stitchTiles="stitch" result="noise" />
                <feColorMatrix type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 1 0" result="coloredNoise" />
            </filter>
        </defs>
        <rect width="100%" height="100%" filter="url(#noise-filter)" />
    </svg>

    <div id="app">
        <x-layouts.app.header />

        <div class="mx-auto my-6 md:my-10 max-w-4xl px-4">
            {{ $slot }}
        </div>

        <x-layouts.app.footer />

        @include('components.layouts.partials.tail')
    </div>
</body>
</html>
