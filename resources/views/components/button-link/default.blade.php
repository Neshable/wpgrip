@props(['elementType' => 'a'])

@php
    $class = 'inline-block cursor-pointer justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md bg-primary-600 hover:bg-primary-700 md:py-4 md:text-lg md:px-10';
@endphp


@if($elementType === 'a')
<a
    {{ $attributes->merge(['class' => $class]) }}
    {{ $attributes }}
>
    {{ $slot }}
</a>
@else
<button
    {{ $attributes->merge(['class' => $class]) }}
    {{ $attributes }}
>
    {{ $slot }}
</button>
@endif
