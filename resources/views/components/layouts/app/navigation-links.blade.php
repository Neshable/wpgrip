<x-nav.item route="features">{{ __('Features') }}</x-nav.item>
<x-nav.item route="pricing">{{ __('Pricing') }}</x-nav.item>
<x-nav.item route="roadmap">{{ __('Roadmap') }}</x-nav.item>

@auth
    <x-nav.item route="dashboard">{{ __('Dashboard') }}</x-nav.item>
@endauth

@guest
    <x-nav.item route="login" class="md:hidden">{{ __('Login') }}</x-nav.item>
@endguest
