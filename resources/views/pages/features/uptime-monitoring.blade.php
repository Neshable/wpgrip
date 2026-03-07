<x-layouts.app>
<x-slot name="title">WordPress Uptime Monitoring — Know Before Your Clients Do</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(37,99,235,0.35) 0%, rgba(37,99,235,0.10) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
            Monitoring
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            Your sites go down.<br><span class="text-blue-400">You find out first.</span>
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            WPGrip checks your WordPress sites around the clock and alerts you the moment something breaks — before your clients notice, before search engines penalize, before revenue stops.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-blue-500 bg-blue-600 text-blue-50 hover:bg-blue-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-blue-900/40">
                Start monitoring for free
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                See pricing
            </a>
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="py-24 bg-[#0a0a0a]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">How it works</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                Three things running in the background.
                <span class="text-neutral-400">Zero plugins on your site.</span>
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                WPGrip monitors from the outside — HTTP checks against your sites on a fixed schedule. No code installed on WordPress, no performance cost, no attack surface added.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
            $steps = [
                ['num' => '01', 'color' => 'blue', 'title' => 'Uptime checks every minute', 'desc' => 'We send an HTTP request to each of your sites every 60 seconds. If a site does not return a 200 response, we flag it and retry before alerting — no false positives.'],
                ['num' => '02', 'color' => 'emerald', 'title' => 'SSL certificate monitoring', 'desc' => 'We check your SSL certificates daily and alert you at 30, 14, and 7 days before expiry. A lapsed certificate means browser warnings and lost trust.'],
                ['num' => '03', 'color' => 'violet', 'title' => 'Domain expiry tracking', 'desc' => 'We track your domain registration dates across your portfolio. Get notified before a domain lapses and someone else registers it.'],
            ];
            @endphp
            @foreach($steps as $s)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-7 flex flex-col gap-4 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-300">
                <span class="text-xs font-mono text-{{ $s['color'] }}-400">{{ $s['num'] }}</span>
                <h3 class="text-lg font-medium text-white leading-snug">{{ $s['title'] }}</h3>
                <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- STATUS DASHBOARD MOCK --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">Dashboard</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Every site. One screen.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    See the health of your entire WordPress portfolio at a glance. Uptime percentages, SSL status, response times, and active alerts — all in a single dashboard view.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Real-time status for every site',
                        'Uptime percentage over 24h, 7d, 30d',
                        'SSL validity and days until expiry',
                        'Response time tracking',
                        'Color-coded health indicators',
                        'Filter by server, client, or status',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-xl border border-white/10 bg-neutral-900 p-6 space-y-2">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs text-neutral-500 font-mono uppercase tracking-wide">Site Status</span>
                    <span class="text-xs text-emerald-400 font-mono">5 sites monitored</span>
                </div>
                @foreach([
                    ['name' => 'client-shop.com',       'status' => 'Operational', 'uptime' => '99.98%', 'c' => 'emerald'],
                    ['name' => 'agency-portfolio.net',   'status' => 'Operational', 'uptime' => '100%',   'c' => 'emerald'],
                    ['name' => 'shop.example.com',      'status' => 'SSL Expiring', 'uptime' => '99.71%', 'c' => 'amber'],
                    ['name' => 'staging.project.io',    'status' => 'Operational', 'uptime' => '99.90%', 'c' => 'emerald'],
                    ['name' => 'legacy-blog.org',       'status' => 'Down 3m ago',  'uptime' => '94.20%', 'c' => 'rose'],
                ] as $site)
                @php
                $dot  = ['emerald'=>'bg-emerald-500','amber'=>'bg-amber-400','rose'=>'bg-rose-500'][$site['c']];
                $txt  = ['emerald'=>'text-emerald-400','amber'=>'text-amber-400','rose'=>'text-rose-400'][$site['c']];
                @endphp
                <div class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.02] px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
                        <span class="text-sm text-neutral-200 font-mono">{{ $site['name'] }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-mono text-neutral-500">{{ $site['uptime'] }}</span>
                        <span class="text-xs {{ $txt }}">{{ $site['status'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ALERTS --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="text-xs text-neutral-500 font-mono">alert timeline</span>
                </div>
                <div class="p-5 space-y-4 font-mono text-xs">
                    <div class="flex items-start gap-3">
                        <span class="text-rose-400 mt-0.5">●</span>
                        <div>
                            <div class="text-neutral-300">legacy-blog.org went down</div>
                            <div class="text-neutral-600">Today at 14:32 · HTTP 502 · Slack + Email sent</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-emerald-400 mt-0.5">●</span>
                        <div>
                            <div class="text-neutral-300">legacy-blog.org recovered</div>
                            <div class="text-neutral-600">Today at 14:35 · Downtime: 3 minutes</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-amber-400 mt-0.5">●</span>
                        <div>
                            <div class="text-neutral-300">shop.example.com SSL expires in 7 days</div>
                            <div class="text-neutral-600">Today at 09:00 · Renewal recommended</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-amber-400 mt-0.5">●</span>
                        <div>
                            <div class="text-neutral-300">client-domain.io domain expires in 14 days</div>
                            <div class="text-neutral-600">Yesterday at 09:00 · Registrar: Namecheap</div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono mb-6">Alerts</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    The right alert. The right channel.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Get notified where you work. WPGrip sends alerts to Slack and email — with enough context to act immediately. No vague "your site might be down" messages. You get the site name, error code, and timestamp.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Slack notifications with full context',
                        'Email alerts with site details',
                        'Downtime and recovery notifications',
                        'SSL expiry warnings at 30, 14, 7 days',
                        'Domain expiry reminders',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-rose-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- NO PLUGIN DIFFERENCE --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f0f0f 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">Zero footprint</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                Other monitoring tools install a plugin on your WordPress.
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                That means extra PHP running on every page load, another dependency to update, and another entry point for attackers. WPGrip monitors from the outside — your WordPress stays clean.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-12 max-w-3xl mx-auto">
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-6">
                <h3 class="text-sm font-medium text-neutral-500 mb-4 uppercase tracking-wide">Plugin-based monitoring</h3>
                <ul class="space-y-3">
                    @foreach(['Adds PHP overhead to every page load', 'Exposes REST API endpoints', 'Must be updated alongside WordPress', 'Breaks during fatal errors', 'Can conflict with other plugins'] as $f)
                    <li class="flex items-center gap-2 text-sm text-neutral-500">
                        <svg class="h-4 w-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-xl border border-emerald-500/20 bg-emerald-950/20 p-6">
                <h3 class="text-sm font-medium text-emerald-400 mb-4 uppercase tracking-wide">WPGrip monitoring</h3>
                <ul class="space-y-3">
                    @foreach(['Zero impact on page load speed', 'No code on your WordPress installation', 'Works even when WordPress crashes', 'No plugin conflicts possible', 'Nothing for attackers to exploit'] as $f)
                    <li class="flex items-center gap-2 text-sm text-neutral-300">
                        <svg class="h-4 w-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(30,64,175,0.15) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-28 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight text-balance">
            Stop finding out from your clients
        </h2>
        <p class="mt-3 text-lg text-neutral-400 font-light">Free trial. No credit card. No plugins to install.</p>
        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-blue-500 bg-blue-600 text-blue-50 hover:bg-blue-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-blue-900/40">
                Start monitoring for free
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                View pricing
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
