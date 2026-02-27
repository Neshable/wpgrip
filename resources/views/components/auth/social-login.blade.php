@props(['before' => ''])

<div {{ $attributes->merge(['class' => '']) }}>
    @if (count($oauthProviders) > 0)
        {{ $before }}
    @endif

    @foreach ($oauthProviders as $oauthProvider)
        <a href="{{ route('auth.oauth.redirect', $oauthProvider->provider_name) }}"
           class="flex items-center justify-center gap-3 w-full rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 text-neutral-200 text-sm font-medium py-2.5 px-4 my-2 transition-colors">
            @svg('colored/' . $oauthProvider->provider_name, 'w-5 h-5')
            <span>{{ __('Continue with ' . $oauthProvider->name) }}</span>
        </a>
    @endforeach
</div>
