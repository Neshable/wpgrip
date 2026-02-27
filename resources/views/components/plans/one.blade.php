@props([
    'popular' => false,
    'link' => '',
    'name' => '',
    'price' => '',
    'interval' => '',
    'description' => '',
])

<div {{ $attributes->merge(['class' => 'relative flex flex-col gap-5 rounded-xl border p-8 transition-all duration-200 ' . ($popular ? 'border-blue-500/50 bg-blue-950/20 shadow-[0_0_40px_rgba(37,99,235,0.12)]' : 'border-white/10 bg-white/[0.02] hover:bg-white/[0.04] hover:border-white/20')]) }}>

    @if ($popular)
    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full border border-blue-500 bg-blue-600 text-white text-xs font-medium">
        <span class="h-1.5 w-1.5 rounded-full bg-blue-200"></span>
        {{ __('Most popular') }}
    </div>
    @endif

    <div>
        <div class="text-sm font-medium text-neutral-400 mb-2">{{ $name }}</div>
        <div class="flex items-end gap-2">
            <span class="text-4xl font-semibold text-white tracking-tight">{{ $price }}</span>
            <span class="text-sm text-neutral-500 mb-1.5">{{ $interval }}</span>
        </div>
    </div>

    <div class="flex flex-col gap-3 flex-1">
        {{ $description }}
    </div>

    <a href="{{ Auth::check() ? $link : '/register' }}" class="mt-2 inline-flex items-center justify-center h-10 w-full rounded-lg border text-sm font-medium transition-all duration-200 {{ $popular ? 'border-blue-500 bg-blue-600 text-white hover:bg-blue-500' : 'border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white' }}">
        {{ __('Start free trial') }}
    </a>
</div>
