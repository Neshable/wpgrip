@props([
    'popular' => false,
    'link' => '',
])
{{-- 
<div {{$attributes->merge(['class' => 'relative px-5 py-10 flex flex-col gap-4 mx-auto text-center border-2 border-primary-500 rounded-2xl transition'])}}>
    @if ($popular)
    <div class="absolute border-0 top-0 -mt-3 left-1/2 transform -translate-x-1/2 bg-primary-500 text-primary-50 mx-auto rounded z-0 text-xs px-2 py-1">
        {{ __('Most popular') }}
    </div>
    @endif

    <x-heading.h3>
        {{ $name }}
    </x-heading.h3>

    <div class="flex flex-col gap-1">
        <div class="text-4xl">
            {{ $price }}
        </div>

        <div class="text-neutral-400 text-sm">
            {{ $interval }}
        </div>
    </div>

    <div class="py-4">
        {{ $description }}
    </div>

    <x-button-link.primary href="{{$link}}">
        {{ __('Buy') }} {{ $name }}
    </x-button-link.primary>
</div> --}}

<div {{$attributes->merge(['class' => 'relative px-8 py-10 xl:py-16 flex flex-col gap-4 mx-auto text-center bg-gray-100 rounded-2xl transition' . ($popular ? ' bg-gradient-to-br from-blue-700 to-blue-900 relative text-white lg:-mt-8 lg:mb-8' : '')])}}>
    @if ($popular)
    <div class="absolute border-0 top-0 -mt-3 left-1/2 transform -translate-x-1/2 bg-white text-black mx-auto rounded z-0 text-xs px-2 py-1">
        {{ __('Most popular') }}
    </div>
    @endif
    <div>
        <x-heading.h3 class="block font-bold text-2xl {{ $popular ? 'text-white' : ''}}">
            {{ $name }}
        </x-heading.h3>

        <span class="flex items-center gap-x-7 font-bold text-[60px]">
            {{ $price }}
            <span class="text-base">{{ $interval }}</span>
        </span>
    </div>

    <div class="flex flex-col gap-y-4">
        {{ $description }}
    </div>

    <x-button-link.primary class="mt-8 {{ $popular ?? 'bg-orange-500'}}" href="{{ Auth::check() ? $link : '/register'}}">
        Start a free trial
    </x-button-link.primary>
</div>