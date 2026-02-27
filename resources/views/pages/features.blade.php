<x-layouts.app>
<x-slot name="title">Features — Everything You Need to Manage WordPress at Scale</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(37,99,235,0.35) 0%, rgba(37,99,235,0.10) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 50% 50% at 80% 20%, rgba(124,58,237,0.12) 0%, transparent 60%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
            SSH-native. Plugin-free. Always on.
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            Everything you need.<br><span class="text-blue-400">Nothing you don’t.</span>
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            WPGrip connects directly to your servers via SSH and WP-CLI — giving you complete control
            over every WordPress site in your portfolio, without installing a single plugin.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-blue-500 bg-blue-600 text-blue-50 hover:bg-blue-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-blue-900/40">
                Start your free trial
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                See pricing
            </a>
        </div>
    </div>
</section>

{{-- ---- PHILOSOPHY ---- --}}
<section class="py-24 bg-[#0a0a0a]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">Why WPGrip</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                Other tools install plugins on your sites.
                <span class="text-neutral-400">We don’t.</span>
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                Every plugin you add to WordPress is another dependency to update, another attack surface to defend,
                another process loading on every page request. WPGrip uses a single SSH key in your server’s
                <code class="text-neutral-300 bg-white/5 px-1.5 py-0.5 rounded text-sm">authorized_keys</code> file.
                That’s it. Your WordPress stays lean. Your visitors stay fast.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
            $pillars = [
                ['tag' => 'Performance', 'color' => 'emerald',
                 'title' => '2× faster than plugin-based tools',
                 'desc' => 'WP-CLI executes over your direct SSH tunnel. No HTTP round-trips, no WordPress bootstrap, no shared-memory overhead. Commands that take seconds elsewhere happen instantly.'],
                ['tag' => 'Security', 'color' => 'blue',
                 'title' => 'Zero attack surface on your WordPress',
                 'desc' => 'No REST API endpoints exposed. No admin credentials stored. No third-party code executing on your server during page loads. An SSH key is cryptographically strong and trivially revokable.'],
                ['tag' => 'Control', 'color' => 'violet',
                 'title' => 'Real commands. Full access.',
                 'desc' => 'Inspect files, check logs, run arbitrary WP-CLI commands, query databases directly — from a single dashboard, across every site simultaneously. Nothing is hidden behind abstractions.'],
            ];
            $colors = [
                'emerald' => ['badge' => 'border-emerald-900 bg-emerald-950 text-emerald-300'],
                'blue'    => ['badge' => 'border-blue-900 bg-blue-950 text-blue-300'],
                'violet'  => ['badge' => 'border-violet-900 bg-violet-950 text-violet-300'],
            ];
            @endphp
            @foreach($pillars as $p)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-7 flex flex-col gap-4 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-300">
                <div class="inline-flex w-fit items-center px-2.5 py-1 rounded-md border text-xs font-mono {{ $colors[$p['color']]['badge'] }}">{{ $p['tag'] }}</div>
                <h3 class="text-lg font-medium text-white leading-snug">{{ $p['title'] }}</h3>
                <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $p['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ---- MONITORING ---- --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">Monitoring</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Know the moment something goes wrong
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Uptime checks, SSL expiry alerts, domain warnings, and PageSpeed regression detection — all
                    running 24/7 across your entire portfolio. When something breaks, you hear about it
                    before your client does.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Uptime monitoring with instant alerts',
                        'SSL certificate expiry warnings (30, 14, 7 days)',
                        'Domain expiry tracking',
                        'Google PageSpeed for mobile & desktop',
                        'Vulnerability scanning for plugins & themes',
                        'Daily regression tests',
                        'Slack & email alert channels',
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
                    <span class="text-xs text-emerald-400 font-mono">All systems monitored</span>
                </div>
                @foreach([
                    ['name' => 'client-site.com',       'status' => 'Operational', 'uptime' => '99.98%', 'c' => 'emerald'],
                    ['name' => 'agency-portfolio.net',  'status' => 'Operational', 'uptime' => '100%',   'c' => 'emerald'],
                    ['name' => 'shop.example.com',      'status' => 'SSL Warning',  'uptime' => '99.71%', 'c' => 'amber'],
                    ['name' => 'staging.project.io',    'status' => 'Operational', 'uptime' => '99.90%', 'c' => 'emerald'],
                    ['name' => 'legacy-blog.org',       'status' => 'Down',         'uptime' => '94.20%', 'c' => 'rose'],
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

{{-- ---- GIT DEPLOYMENTS ---- --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">deployment log</span>
                </div>
                <div class="p-6 font-mono text-xs space-y-2">
                    <div class="text-neutral-500">→ Webhook received · github/acme/client-theme · push main</div>
                    <div class="text-blue-400">→ Pulling commit <span class="text-neutral-300">a3f9c12</span> <span class="text-neutral-500">“Update hero section”</span></div>
                    <div class="text-neutral-400">→ composer install --no-dev...</div>
                    <div class="text-neutral-400">→ npm run build...</div>
                    <div class="text-neutral-400">→ wp cache flush</div>
                    <div class="text-emerald-400">✓ Deployed to 3 sites in 22s</div>
                    <div class="mt-5 pt-5 border-t border-white/5">
                        <div class="text-neutral-500 mb-3">Shared repositories</div>
                        @foreach([
                            ['r'=>'github / acme / theme',   'b'=>'main',       's'=>3],
                            ['r'=>'gitlab / agency / plugin','b'=>'production', 's'=>7],
                            ['r'=>'bitbucket / client / wp', 'b'=>'main',       's'=>1],
                        ] as $row)
                        <div class="flex justify-between items-center py-2 border-b border-white/5 last:border-0">
                            <span class="text-neutral-300">{{ $row['r'] }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-neutral-500">{{ $row['b'] }}</span>
                                <span class="text-xs text-neutral-600">{{ $row['s'] }} site{{ $row['s']>1?'s':'' }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-violet-900 bg-violet-950 text-violet-300 text-xs font-mono mb-6">Git Deployments</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Push to deploy.<br>Across every site.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Connect GitHub, GitLab, or Bitbucket repos to any of your sites. One repo can power dozens
                    of sites simultaneously. Deploy on push via webhooks, or manually with a single click.
                    Full deployment log, rollback to any commit.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'GitHub, GitLab & Bitbucket',
                        'Webhook-triggered auto deployments',
                        'One repo shared across multiple sites',
                        'Manual deploy with real-time log',
                        'Rollback to any previous commit',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-violet-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ---- AI ASSISTANT ---- --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0d1220 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">AI Assistant</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Your WordPress expert.<br>Available 24/7.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    The AI assistant knows your site — its WP version, PHP version, active plugins,
                    server configuration, and historical PageSpeed scores. Ask it anything in plain English.
                    It can diagnose issues, recommend fixes, and even run WP-CLI commands over SSH to
                    investigate and resolve problems in real time.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Full site context — versions, plugins, server info',
                        'Diagnoses plugin conflicts & vulnerabilities',
                        'PageSpeed optimisation recommendations',
                        'Runs SSH commands to investigate live issues',
                        'Agentic loop — up to 5 tool-call rounds',
                        'Available on every site, every plan',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <div class="h-2 w-2 rounded-full bg-blue-400 animate-pulse"></div>
                    <span class="text-xs text-neutral-400">AI Assistant — client-site.com</span>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 h-7 w-7 rounded-full bg-neutral-700 flex items-center justify-center text-xs text-neutral-300">U</div>
                        <div class="bg-white/5 rounded-xl rounded-tl-sm px-4 py-3 text-sm text-neutral-300 leading-relaxed max-w-xs">Why is my PageSpeed score dropping on mobile?</div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <div class="bg-blue-600/20 border border-blue-500/20 rounded-xl rounded-tr-sm px-4 py-3 text-sm text-blue-100 leading-relaxed max-w-sm">
                            I can see the score dropped from 78 to 61 over 7 days. The Yoast SEO update (v21.9) added 48kB of undeferred JS. I recommend deferring <code class="text-blue-300">seo-schema.js</code>. Want me to check the file size via SSH?
                        </div>
                        <div class="flex-shrink-0 h-7 w-7 rounded-full bg-blue-600 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 h-7 w-7 rounded-full bg-neutral-700 flex items-center justify-center text-xs text-neutral-300">U</div>
                        <div class="bg-white/5 rounded-xl rounded-tl-sm px-4 py-3 text-sm text-neutral-300">Yes, check it.</div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <div class="bg-blue-600/20 border border-blue-500/20 rounded-xl rounded-tr-sm px-4 py-3 text-sm text-blue-100 leading-relaxed max-w-sm">
                            <div class="text-xs text-blue-400 font-mono mb-2">$ wc -c seo-schema.js</div>
                            <div class="font-mono text-xs text-neutral-300 mb-2">49,152 bytes</div>
                            Confirmed — 48kB unminified. Rolling back to v21.8 is safest. Want me to queue that update?
                        </div>
                        <div class="flex-shrink-0 h-7 w-7 rounded-full bg-blue-600 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-4">
                    <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2">
                        <span class="text-sm text-neutral-600 flex-1">Ask anything about your site...</span>
                        <svg class="h-4 w-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3-3 3m-6 0h9"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ---- UPDATES & BACKUPS ---- --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-8">
            {{-- Updates --}}
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-8">
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">Updates</div>
                <h3 class="text-2xl font-semibold text-white mb-4">One-click updates across your fleet</h3>
                <p class="text-neutral-400 font-light text-sm leading-relaxed mb-6">
                    Update plugins, themes, and WordPress core on a single site or across your entire portfolio
                    at once. WP-CLI runs the updates directly over SSH — fast, reliable, no admin panel needed.
                </p>
                <ul class="space-y-2">
                    @foreach(['Update plugins, themes & WP core', 'Bulk updates across multiple sites', 'Per-site or portfolio-wide control', 'Vulnerability alerts before you update'] as $f)
                    <li class="flex items-center gap-2 text-sm text-neutral-300">
                        <svg class="h-4 w-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
            {{-- Backups --}}
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-8">
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-amber-900 bg-amber-950 text-amber-300 text-xs font-mono mb-6">Backups</div>
                <h3 class="text-2xl font-semibold text-white mb-4">Encrypted cloud database backups</h3>
                <p class="text-neutral-400 font-light text-sm leading-relaxed mb-6">
                    Automated, encrypted database backups on your schedule. Stored securely in the cloud.
                    Restore to any point in time with a single click — no SSH required for the restore.
                </p>
                <ul class="space-y-2">
                    @foreach(['Encrypted at rest and in transit', 'Flexible backup schedules', 'One-click restore', 'Offsite cloud storage'] as $f)
                    <li class="flex items-center gap-2 text-sm text-neutral-300">
                        <svg class="h-4 w-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ---- FULL FEATURE GRID ---- --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f0f0f 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">Full Feature List</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight">All features. Every plan.</h2>
            <p class="mt-4 text-neutral-400 font-light max-w-xl mx-auto">No feature gates. No tier exclusions. Everything below is included from day one.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
            $features = [
                ['icon' => '📡', 'tag' => 'Monitoring',   'title' => 'Uptime Monitoring',         'desc' => 'Continuous checks with instant Slack & email alerts the moment a site goes offline.'],
                ['icon' => '🔒', 'tag' => 'Monitoring',   'title' => 'SSL Certificate Tracking',  'desc' => 'Warnings at 30, 14, and 7 days before expiry. Never let a certificate lapse again.'],
                ['icon' => '🌐', 'tag' => 'Monitoring',   'title' => 'Domain Expiry Alerts',      'desc' => 'Track domain registration expiry across your entire portfolio in one place.'],
                ['icon' => '⚡', 'tag' => 'Performance',  'title' => 'PageSpeed Tracking',        'desc' => 'Google PageSpeed scores for mobile and desktop, with full history and regression alerts.'],
                ['icon' => '🛡️', 'tag' => 'Security',   'title' => 'Vulnerability Scanning',    'desc' => 'Continuously updated database detects vulnerable plugins, themes, and WP core versions.'],
                ['icon' => '🤖', 'tag' => 'AI',          'title' => 'AI Assistant',              'desc' => 'Context-aware AI that knows your site and can run SSH commands to diagnose issues.'],
                ['icon' => '🚀', 'tag' => 'Deployments', 'title' => 'Git Deployments',           'desc' => 'GitHub, GitLab, Bitbucket. Webhook-triggered or manual. One repo across many sites.'],
                ['icon' => '🗄️', 'tag' => 'Backups',    'title' => 'Database Backups',          'desc' => 'Encrypted, automated, offsite. Schedule backups and restore with one click.'],
                ['icon' => '🔄', 'tag' => 'Management', 'title' => 'One-Click Updates',         'desc' => 'Update plugins, themes, and core via WP-CLI across all sites simultaneously.'],
                ['icon' => '👥', 'tag' => 'Teams',       'title' => 'Team Collaboration',        'desc' => 'Invite members to your workspace or accept invitations to manage other teams\' sites.'],
                ['icon' => '📊', 'tag' => 'Analytics',   'title' => 'Performance History',       'desc' => 'Historical trend data for all monitoring metrics — spot regressions before they become problems.'],
                ['icon' => '🖥️', 'tag' => 'Management', 'title' => 'Server & Client Management','desc' => 'Organise sites by client or server. Full server metadata and provider tracking.'],
            ];
            $tagColors = [
                'Monitoring'   => 'text-blue-400 bg-blue-950/50',
                'Performance'  => 'text-emerald-400 bg-emerald-950/50',
                'Security'     => 'text-rose-400 bg-rose-950/50',
                'AI'           => 'text-violet-400 bg-violet-950/50',
                'Deployments'  => 'text-indigo-400 bg-indigo-950/50',
                'Backups'      => 'text-amber-400 bg-amber-950/50',
                'Management'   => 'text-neutral-300 bg-neutral-800/50',
                'Teams'        => 'text-cyan-400 bg-cyan-950/50',
                'Analytics'    => 'text-teal-400 bg-teal-950/50',
            ];
            @endphp
            @foreach($features as $f)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-6 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-200 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">{{ $f['icon'] }}</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded {{ $tagColors[$f['tag']] ?? 'text-neutral-400 bg-neutral-800/50' }}">{{ $f['tag'] }}</span>
                </div>
                <h3 class="text-sm font-medium text-white">{{ $f['title'] }}</h3>
                <p class="text-sm text-neutral-500 font-light leading-relaxed">{{ $f['desc'] }}</p>
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
            Take control of your WordPress portfolio
        </h2>
        <p class="mt-3 text-lg text-neutral-400 font-light">Free trial. No credit card. No plugins to install.</p>
        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-blue-500 bg-blue-600 text-blue-50 hover:bg-blue-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-blue-900/40">
                Get started for free
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                View pricing
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
