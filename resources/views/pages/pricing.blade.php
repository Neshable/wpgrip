<x-layouts.app>
<x-slot name="title">Pricing — Simple, Transparent Plans</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(37,99,235,0.35) 0%, rgba(37,99,235,0.10) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
            No hidden fees. Cancel anytime.
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-2xl">
            Simple pricing.<br><span class="text-blue-400">Powerful features.</span>
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            Three plans, priced by how many sites you manage.
            Scale up when you're ready.
        </p>
    </div>
</section>

{{-- PLANS --}}
<section class="bg-[#0a0a0a] pt-4 pb-24">
    <div class="max-w-screen-xl mx-auto px-6">
        @php
        $plans = [
            [
                'name'     => 'Basic',
                'slug'     => 'basic-monthly',
                'price'    => '$10',
                'popular'  => false,
                'limit'    => 'Up to 5 sites',
                'features' => [
                    ['label' => '5 WordPress sites',          'included' => true],
                    ['label' => 'Uptime monitoring',           'included' => true],
                    ['label' => 'SSL & domain expiry alerts',  'included' => true],
                    ['label' => 'PageSpeed tracking',          'included' => true],
                    ['label' => 'Vulnerability scanning',      'included' => true],
                    ['label' => 'One-click plugin/theme updates', 'included' => true],
                    ['label' => 'Database backups',            'included' => true],
                    ['label' => 'Git deployments',             'included' => false],
                    ['label' => 'AI Assistant',                'included' => false],
                    ['label' => 'Team members',                'included' => false],
                ],
            ],
            [
                'name'     => 'Pro',
                'slug'     => 'pro-monthly',
                'price'    => '$25',
                'popular'  => true,
                'limit'    => 'Up to 25 sites',
                'features' => [
                    ['label' => '25 WordPress sites',          'included' => true],
                    ['label' => 'Uptime monitoring',           'included' => true],
                    ['label' => 'SSL & domain expiry alerts',  'included' => true],
                    ['label' => 'PageSpeed tracking',          'included' => true],
                    ['label' => 'Vulnerability scanning',      'included' => true],
                    ['label' => 'One-click plugin/theme updates', 'included' => true],
                    ['label' => 'Database backups',            'included' => true],
                    ['label' => 'Git deployments',             'included' => true],
                    ['label' => 'AI Assistant',                'included' => true],
                    ['label' => 'Up to 3 team members',        'included' => true],
                ],
            ],
            [
                'name'     => 'Ultimate',
                'slug'     => 'ultimate-monthly',
                'price'    => '$50',
                'popular'  => false,
                'limit'    => 'Unlimited sites',
                'features' => [
                    ['label' => 'Unlimited WordPress sites',   'included' => true],
                    ['label' => 'Uptime monitoring',           'included' => true],
                    ['label' => 'SSL & domain expiry alerts',  'included' => true],
                    ['label' => 'PageSpeed tracking',          'included' => true],
                    ['label' => 'Vulnerability scanning',      'included' => true],
                    ['label' => 'One-click plugin/theme updates', 'included' => true],
                    ['label' => 'Database backups',            'included' => true],
                    ['label' => 'Git deployments',             'included' => true],
                    ['label' => 'AI Assistant',                'included' => true],
                    ['label' => 'Unlimited team members',      'included' => true],
                ],
            ],
        ];
        @endphp

        <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            @foreach($plans as $plan)
            @php $popular = $plan['popular']; @endphp
            <div class="relative flex flex-col rounded-xl border p-8 transition-all duration-200 {{ $popular ? 'border-blue-500/50 bg-blue-950/20 shadow-[0_0_40px_rgba(37,99,235,0.12)]' : 'border-white/10 bg-white/[0.02]' }}">

                @if($popular)
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full border border-blue-500 bg-blue-600 text-white text-xs font-medium whitespace-nowrap">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-200"></span>
                    Most popular
                </div>
                @endif

                {{-- Name + price --}}
                <div class="mb-6">
                    <div class="text-sm font-medium text-neutral-400 mb-3">{{ $plan['name'] }}</div>
                    <div class="flex items-end gap-1.5 mb-1">
                        <span class="text-4xl font-semibold text-white tracking-tight">{{ $plan['price'] }}</span>
                        <span class="text-sm text-neutral-500 mb-1.5">/ month</span>
                    </div>
                    <div class="text-xs text-neutral-500 mt-1">{{ $plan['limit'] }}</div>
                </div>

                {{-- Features --}}
                <ul class="flex flex-col gap-3 flex-1 mb-8">
                    @foreach($plan['features'] as $f)
                    <li class="flex items-start gap-3 text-sm {{ $f['included'] ? 'text-neutral-300' : 'text-neutral-600' }}">
                        @if($f['included'])
                        <svg class="h-4 w-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                        <svg class="h-4 w-4 text-neutral-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                        {{ $f['label'] }}
                    </li>
                    @endforeach
                </ul>

                {{-- CTA --}}
                <a href="{{ Auth::check() ? route('checkout.subscription', $plan['slug']) : '/register' }}"
                   class="inline-flex items-center justify-center h-10 w-full rounded-lg border text-sm font-medium transition-all duration-200 {{ $popular ? 'border-blue-500 bg-blue-600 text-white hover:bg-blue-500' : 'border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white' }}">
                    Start free trial
                </a>
            </div>
            @endforeach
        </div>

        <p class="text-center text-xs text-neutral-600 mt-8">5-day free trial &middot; No credit card required &middot; Cancel anytime</p>
    </div>
</section>



{{-- COLLABORATION NOTE --}}
<section class="py-16 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f0f0f 100%)">
    <div class="max-w-3xl mx-auto px-6">
        <div class="rounded-xl border border-blue-500/20 bg-blue-500/5 p-8 flex flex-col md:flex-row items-start md:items-center gap-6">
            <div class="text-3xl">👥</div>
            <div class="flex-1">
                <h3 class="text-lg font-medium text-white mb-2">Just collaborating?</h3>
                <p class="text-sm text-neutral-400 font-light leading-relaxed">
                    A free account is all you need. Other members can invite you to their workspaces — once they do,
                    you’ll have full access to view and manage sites in that workspace.
                    <strong class="text-neutral-200">You don’t need a paid plan to accept workspace invitations.</strong>
                </p>
            </div>
            <a href="/register" class="flex-shrink-0 inline-flex items-center justify-center h-10 px-5 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-colors">
                Register free
            </a>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-3xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-semibold text-white tracking-tight">Frequently asked questions</h2>
        </div>
        <div class="space-y-3">
            @php
            $faqs = [
                ['q' => 'How long is the free trial?', 'a' => 'We offer a 5-day free trial with full access to all features. No credit card required to start.'],
                ['q' => 'What happens when my trial ends?', 'a' => 'Your account moves to the free plan automatically. You\'ll be notified beforehand so you can choose a paid plan if you need to keep full access.'],
                ['q' => 'Can I change my plan later?', 'a' => 'Yes — upgrade or downgrade at any time directly from your dashboard. Changes take effect immediately with prorated billing.'],
                ['q' => 'Do you offer refunds?', 'a' => 'Yes. We offer a 30-day money-back guarantee on all annual subscriptions. If you\'re not happy, just ask.'],
                ['q' => 'Which payment methods do you support?', 'a' => 'We accept all major credit cards and PayPal. All payments are processed securely.'],
                ['q' => 'Is there a setup fee?', 'a' => 'None. You\'re up and running the moment you add an SSH key to your server — no additional costs.'],
                ['q' => 'Can I manage sites across different hosting providers?', 'a' => 'Absolutely. WPGrip is hosting-agnostic — DigitalOcean, Hetzner, AWS, Kinsta, WP Engine, Cloudways, or any VPS with SSH access.'],
            ];
            @endphp
            @foreach($faqs as $i => $faq)
            <div x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }" class="rounded-xl border border-white/5" :class="open ? 'border-white/10 bg-white/[0.03]' : 'hover:border-white/8 hover:bg-white/[0.02]'">
                <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-5 text-left gap-4">
                    <span class="text-sm md:text-base font-medium leading-snug" :class="open ? 'text-white' : 'text-neutral-400'">{{ $faq['q'] }}</span>
                    <span class="flex-shrink-0 h-5 w-5 rounded-full border border-white/20 flex items-center justify-center">
                        <svg class="h-3 w-3 text-neutral-400 transition-transform duration-200" :class="open ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                </button>
                <div x-show="open" x-collapse class="px-6 pb-5">
                    <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(30,64,175,0.15) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-28 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight text-balance">
            Start managing smarter today
        </h2>
        <p class="mt-3 text-lg text-neutral-400 font-light">Free trial. No credit card. Cancel anytime.</p>
        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-blue-500 bg-blue-600 text-blue-50 hover:bg-blue-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-blue-900/40">
                Get started for free
            </a>
            <a href="{{ route('features') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                Explore features
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
