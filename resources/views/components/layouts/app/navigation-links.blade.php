<x-nav.item route="#features">{{ __('Features') }}</x-nav.item>
<x-nav.item route="pricing">{{ __('Pricing') }}</x-nav.item>
<x-nav.item route="roadmap">{{ __('Roadmap') }}</x-nav.item>

{{-- @auth
    @if (auth()->user()->tenants()->count() > 0)
    <x-link href="{{ route('dashboard') }}" class="!px-2">
            {{ __('Dashboard') }}
    </x-link>
    @endif
@endauth --}}

@guest
    <x-nav.item route="login" class="md:hidden">{{ __('Login') }}</x-nav.item>
@endguest
