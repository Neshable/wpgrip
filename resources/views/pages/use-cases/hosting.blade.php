<x-layouts.app>
<x-slot name="title">WordPress Management for Hosting Companies — Monitor and Manage Customer Sites</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(6,182,212,0.25) 0%, rgba(6,182,212,0.08) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-cyan-500/30 bg-cyan-500/10 text-cyan-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-cyan-400"></span>
            For Hosting Companies
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            Your servers.<br><span class="text-cyan-400">Your customers' WordPress.</span>
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            You run the infrastructure. Your customers run WordPress on it. WPGrip lets you monitor, secure, and manage every WordPress installation across your fleet — via SSH, without touching their WordPress admin.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-cyan-500 bg-cyan-600 text-cyan-50 hover:bg-cyan-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-cyan-900/40">
                Start your free trial
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                See pricing
            </a>
        </div>
    </div>
</section>

{{-- VALUE PROPS --}}
<section class="py-24 bg-[#0a0a0a]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">Why hosting companies use WPGrip</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                WordPress is your customers' problem.
                <span class="text-neutral-400">Until it becomes yours.</span>
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                Outdated plugins cause security incidents. Unpatched core versions get exploited. Bloated databases slow down your shared servers. WPGrip gives you visibility and control over every WordPress installation on your infrastructure.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
            $values = [
                ['icon' => '🔍', 'title' => 'Fleet-wide visibility', 'desc' => 'See every WordPress site across all your servers. PHP versions, WP versions, plugin counts, disk usage, database sizes — all in one dashboard.'],
                ['icon' => '🛡️', 'title' => 'Proactive security', 'desc' => 'Know which customer sites have vulnerable plugins before they lead to support tickets or compromised servers. Daily vulnerability database sync.'],
                ['icon' => '📊', 'title' => 'Performance tracking', 'desc' => 'Track PageSpeed scores and response times across your fleet. Identify resource-heavy sites before they impact other customers on shared infrastructure.'],
            ];
            @endphp
            @foreach($values as $v)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-7 flex flex-col gap-4 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-300">
                <span class="text-2xl">{{ $v['icon'] }}</span>
                <h3 class="text-lg font-medium text-white leading-snug">{{ $v['title'] }}</h3>
                <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $v['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SSH NATIVE --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-cyan-900 bg-cyan-950 text-cyan-300 text-xs font-mono mb-6">SSH-native</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    You already have SSH access. That's all WPGrip needs.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    No plugins installed on customer WordPress sites. No REST API endpoints exposed. No additional attack surface. WPGrip works entirely over SSH using WP-CLI — the same tools your support team already uses.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Works with any server you have SSH access to',
                        'No WordPress plugins needed on customer sites',
                        'Uses WP-CLI — the WordPress command-line standard',
                        'Encrypted SSH tunnel for all operations',
                        'Supports multiple servers and hosting providers',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-cyan-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">fleet overview</span>
                </div>
                <div class="p-5 font-mono text-xs space-y-2">
                    <div class="text-neutral-500"># Sites across 4 servers</div>
                    <div class="text-neutral-500 mt-2">SERVER           SITES  WP OUTDATED  VULNS</div>
                    <div class="text-neutral-300">prod-us-east-1     12        1        3</div>
                    <div class="text-neutral-300">prod-eu-west-1      8        0        1</div>
                    <div class="text-amber-400">shared-01          23        5        8</div>
                    <div class="text-neutral-300">staging-01          4        0        0</div>
                    <div class="mt-3 pt-3 border-t border-white/5 text-neutral-500">
                        Total: 47 sites · 6 outdated · 12 vulnerabilities
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(6,182,212,0.12) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-28 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight text-balance">
            See every WordPress site on your infrastructure
        </h2>
        <p class="mt-3 text-lg text-neutral-400 font-light">Free trial. No credit card. No plugins on customer sites.</p>
        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-cyan-500 bg-cyan-600 text-cyan-50 hover:bg-cyan-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-cyan-900/40">
                Start your free trial
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                View pricing
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
