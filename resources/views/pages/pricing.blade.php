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
            Every plan includes the full feature set. No paywalls on core functionality.
            Pay for what you need, scale when you're ready.
        </p>
    </div>
</section>

{{-- PLANS --}}
<section class="bg-[#0a0a0a] pt-4 pb-24">
    <div class="max-w-screen-xl mx-auto px-6">

        {{-- Dark wrapper for the plans component --}}
        <div class="[&_.section-hero]:hidden
                    [&_section]:bg-transparent
                    [&_.card]:bg-neutral-900
                    [&_h2]:text-white
                    [&_h3]:text-white
                    [&_p]:text-neutral-400">
            <x-plans.all calculate-saving-rates="true" preselected-interval="month"></x-plans.all>
        </div>
    </div>
</section>

{{-- EVERYTHING INCLUDED --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">Every Plan</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Everything included.<br>No feature paywalls.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    We don't lock core features behind higher tiers. Every plan gives you access to the full
                    WPGrip toolkit — monitoring, AI assistant, git deployments, backups, and more.
                    The only difference is how many sites you manage.
                </p>
                <div class="mt-8 flex flex-col gap-3">
                    <a href="/register" class="inline-flex items-center justify-center h-11 px-6 w-fit rounded-lg border border-blue-500 bg-blue-600 text-blue-50 hover:bg-blue-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-blue-900/40">
                        Start free trial
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @php
                $allFeatures = [
                    ['icon' => '📡', 'label' => 'Uptime Monitoring'],
                    ['icon' => '🔒', 'label' => 'SSL Tracking'],
                    ['icon' => '🌐', 'label' => 'Domain Expiry Alerts'],
                    ['icon' => '⚡', 'label' => 'PageSpeed Scores'],
                    ['icon' => '🛡️', 'label' => 'Vulnerability Scanning'],
                    ['icon' => '🤖', 'label' => 'AI Assistant'],
                    ['icon' => '🚀', 'label' => 'Git Deployments'],
                    ['icon' => '🗄️', 'label' => 'Database Backups'],
                    ['icon' => '🔄', 'label' => 'One-Click Updates'],
                    ['icon' => '👥', 'label' => 'Team Collaboration'],
                    ['icon' => '📊', 'label' => 'Performance History'],
                    ['icon' => '🔔', 'label' => 'Slack & Email Alerts'],
                ];
                @endphp
                @foreach($allFeatures as $f)
                <div class="flex items-center gap-3 rounded-lg border border-white/5 bg-white/[0.02] px-4 py-3">
                    <span class="text-lg">{{ $f['icon'] }}</span>
                    <span class="text-sm text-neutral-300">{{ $f['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
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
                ['q' => 'Are there annual plans available?', 'a' => 'Yes — annual plans are available at a discounted rate. You can switch between monthly and annual billing from your account settings.'],
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
