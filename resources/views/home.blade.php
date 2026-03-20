<x-layouts.app>
<x-slot name="title">WordPress Site Management — All Your Sites, One Control Panel</x-slot>

{{-- ===================== HERO ===================== --}}
<section class="relative w-full overflow-hidden bg-neutral-950">

    {{-- Dot grid --}}
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>

    {{-- Blue radial glow top-left --}}
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 70% 70% at -10% 50%, rgba(37,99,235,0.28) 0%, transparent 65%);"></div>

    {{-- Purple tint right --}}
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 50% 60% at 100% 40%, rgba(124,58,237,0.13) 0%, transparent 60%);"></div>

    {{-- Bottom fade --}}
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-40" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 pt-40 pb-20">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {{-- Left: text + CTAs --}}
            <div class="flex flex-col items-center text-center lg:items-start lg:text-left">

                <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-xs font-medium tracking-wide uppercase">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                    SSH-native &middot; Zero plugins &middot; AI-powered
                </div>

                <h1 class="text-5xl md:text-6xl font-medium tracking-tight text-white text-balance leading-[1.08]">
                    All Your WordPress Sites.<br>
                    <span class="text-blue-400">One Control Panel.</span>
                </h1>

                <p class="mt-6 text-lg text-neutral-400 font-light max-w-lg text-balance leading-relaxed">
                    Direct SSH access. WP-CLI commands. AI-powered diagnostics.
                    Manage every site in your portfolio from one place — no plugins, no agents, no attack surface.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center gap-3">
                    <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-blue-500 bg-blue-600 text-blue-50 hover:bg-blue-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-blue-900/40">
                        Start free trial
                    </a>
                    <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                        See pricing
                    </a>
                </div>
            </div>

            {{-- Right: SVG network diagram --}}
            <div class="flex items-center justify-center">
                <div class="w-full max-w-lg rounded-xl border border-white/10 bg-white/[0.03] p-3 shadow-2xl shadow-black/60">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500" class="w-full">
                        <defs>
                            <filter id="hero-glow" x="-20%" y="-20%" width="140%" height="140%">
                                <feGaussianBlur stdDeviation="5" result="blur"/>
                                <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                            </filter>
                            <linearGradient id="hero-hubGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#1e293b"/>
                                <stop offset="100%" stop-color="#0f172a"/>
                            </linearGradient>
                            <linearGradient id="hero-siteGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#ffffff"/>
                                <stop offset="100%" stop-color="#f8fafc"/>
                            </linearGradient>
                        </defs>
                        <style>
                            .hero-ssh  { stroke: #94a3b8; stroke-width: 2; stroke-dasharray: 6 6; animation: heroDash 20s linear infinite; }
                            .hero-pkt  { fill: #10b981; filter: drop-shadow(0 0 4px #10b981); }
                            @keyframes heroDash { to { stroke-dashoffset: -200; } }
                        </style>

                        <rect width="100%" height="100%" fill="transparent"/>

                        {{-- Lines --}}
                        <path d="M 400 250 L 180 120" class="hero-ssh"/>
                        <path d="M 400 250 L 620 120" class="hero-ssh"/>
                        <path d="M 400 250 L 180 380" class="hero-ssh"/>
                        <path d="M 400 250 L 620 380" class="hero-ssh"/>

                        {{-- Data packets --}}
                        <circle r="4" class="hero-pkt"><animateMotion dur="3s"   repeatCount="indefinite" path="M 400 250 L 180 120"/></circle>
                        <circle r="4" class="hero-pkt"><animateMotion dur="4s"   repeatCount="indefinite" path="M 620 120 L 400 250"/></circle>
                        <circle r="4" class="hero-pkt"><animateMotion dur="3.5s" repeatCount="indefinite" path="M 400 250 L 180 380"/></circle>
                        <circle r="4" class="hero-pkt"><animateMotion dur="2.5s" repeatCount="indefinite" path="M 620 380 L 400 250"/></circle>

                        {{-- Lock icons --}}
                        <g fill="#10b981">
                            <g transform="translate(270,165) scale(0.6)"><rect x="10" y="12" width="16" height="12" rx="2"/><path d="M12 12 V 8 A 6 6 0 0 1 24 8 V 12" fill="none" stroke="#10b981" stroke-width="3"/></g>
                            <g transform="translate(490,165) scale(0.6)"><rect x="10" y="12" width="16" height="12" rx="2"/><path d="M12 12 V 8 A 6 6 0 0 1 24 8 V 12" fill="none" stroke="#10b981" stroke-width="3"/></g>
                            <g transform="translate(270,295) scale(0.6)"><rect x="10" y="12" width="16" height="12" rx="2"/><path d="M12 12 V 8 A 6 6 0 0 1 24 8 V 12" fill="none" stroke="#10b981" stroke-width="3"/></g>
                            <g transform="translate(490,295) scale(0.6)"><rect x="10" y="12" width="16" height="12" rx="2"/><path d="M12 12 V 8 A 6 6 0 0 1 24 8 V 12" fill="none" stroke="#10b981" stroke-width="3"/></g>
                        </g>

                        {{-- Site nodes --}}
                        <g transform="translate(130,90)">
                            <rect width="100" height="60" rx="6" fill="url(#hero-siteGrad)" stroke="#cbd5e1" stroke-width="2"/>
                            <circle cx="15" cy="15" r="4" fill="#ef4444"/><circle cx="27" cy="15" r="4" fill="#eab308"/><circle cx="39" cy="15" r="4" fill="#22c55e"/>
                            <rect x="15" y="30" width="70" height="4" rx="2" fill="#cbd5e1"/><rect x="15" y="40" width="50" height="4" rx="2" fill="#cbd5e1"/>
                            <text x="50" y="78" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569" text-anchor="middle">Client Site</text>
                        </g>
                        <g transform="translate(570,90)">
                            <rect width="100" height="60" rx="6" fill="url(#hero-siteGrad)" stroke="#cbd5e1" stroke-width="2"/>
                            <circle cx="15" cy="15" r="4" fill="#ef4444"/><circle cx="27" cy="15" r="4" fill="#eab308"/><circle cx="39" cy="15" r="4" fill="#22c55e"/>
                            <rect x="15" y="30" width="70" height="4" rx="2" fill="#cbd5e1"/><rect x="15" y="40" width="40" height="4" rx="2" fill="#cbd5e1"/>
                            <text x="50" y="78" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569" text-anchor="middle">Agency Shop</text>
                        </g>
                        <g transform="translate(130,350)">
                            <rect width="100" height="60" rx="6" fill="url(#hero-siteGrad)" stroke="#cbd5e1" stroke-width="2"/>
                            <circle cx="15" cy="15" r="4" fill="#ef4444"/><circle cx="27" cy="15" r="4" fill="#eab308"/><circle cx="39" cy="15" r="4" fill="#22c55e"/>
                            <rect x="15" y="30" width="60" height="4" rx="2" fill="#cbd5e1"/><rect x="15" y="40" width="55" height="4" rx="2" fill="#cbd5e1"/>
                            <text x="50" y="78" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569" text-anchor="middle">Staging WP</text>
                        </g>
                        <g transform="translate(570,350)">
                            <rect width="100" height="60" rx="6" fill="url(#hero-siteGrad)" stroke="#cbd5e1" stroke-width="2"/>
                            <circle cx="15" cy="15" r="4" fill="#ef4444"/><circle cx="27" cy="15" r="4" fill="#eab308"/><circle cx="39" cy="15" r="4" fill="#22c55e"/>
                            <rect x="15" y="30" width="75" height="4" rx="2" fill="#cbd5e1"/><rect x="15" y="40" width="30" height="4" rx="2" fill="#cbd5e1"/>
                            <text x="50" y="78" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569" text-anchor="middle">Legacy Blog</text>
                        </g>

                        {{-- Central hub --}}
                        <g transform="translate(300,190)">
                            <rect width="200" height="120" rx="8" fill="url(#hero-hubGrad)" filter="url(#hero-glow)"/>
                            <rect width="200" height="24" rx="8" fill="#334155"/>
                            <circle cx="16" cy="12" r="4" fill="#ef4444"/><circle cx="28" cy="12" r="4" fill="#eab308"/><circle cx="40" cy="12" r="4" fill="#22c55e"/>
                            <text x="16" y="50"  font-family="monospace" font-size="12" fill="#10b981">~ wp-grip connect</text>
                            <text x="16" y="70"  font-family="monospace" font-size="12" fill="#94a3b8">Establishing SSH...</text>
                            <text x="16" y="90"  font-family="monospace" font-size="12" fill="#38bdf8">Monitoring active.</text>
                            <text x="100" y="145" font-family="sans-serif" font-size="18" font-weight="900" fill="#0f172a" text-anchor="middle" letter-spacing="1">WPGRIP</text>
                        </g>
                    </svg>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ===================== TRUST BAR ===================== --}}
<section class="border-y border-white/5 bg-neutral-900/40 py-6">
    <div class="max-w-screen-xl mx-auto px-6">
        <p class="text-center text-xs text-neutral-500 uppercase tracking-widest mb-6">Built for developers &amp; agencies who manage WordPress at scale</p>
        <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
            @foreach([
                ['label' => 'SSH Encrypted', 'icon' => '🔐'],
                ['label' => 'Zero Plugins Required', 'icon' => '⚡'],
                ['label' => 'WP-CLI Powered', 'icon' => '🖥️'],
                ['label' => 'Real-Time Monitoring', 'icon' => '📡'],
                ['label' => 'Git Deployments', 'icon' => '🚀'],
                ['label' => 'AI Insights', 'icon' => '🤖'],
            ] as $item)
                <div class="flex items-center gap-2 text-neutral-400 text-sm">
                    <span>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== CORE VALUE PROP ===================== --}}
<section class="py-24 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center justify-center px-3 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-medium font-mono mb-6">Why WPGrip</div>
            <h2 class="text-4xl md:text-5xl font-medium tracking-tight text-white text-balance leading-tight">
                The right way to manage WordPress sites
            </h2>
            <p class="mt-4 text-lg text-neutral-400 font-light leading-relaxed">
                Other tools install plugins — adding security risk, update debt, and overhead to every site they touch.
                WPGrip connects via SSH and WP-CLI directly. Your sites stay lean. Your data stays yours.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
            $cards = [
                [
                    'tag' => 'Performance',
                    'title' => '2× faster than plugin-based tools',
                    'desc' => 'WP-CLI over SSH runs directly on your server. No HTTP round-trips, no plugin overhead, no bottlenecks.',
                    'color' => 'emerald',
                ],
                [
                    'tag' => 'Security',
                    'title' => 'Zero attack surface',
                    'desc' => 'One SSH key in authorized_keys is all WPGrip needs. No stored admin credentials, no REST API exposure, no third-party code on your site.',
                    'color' => 'blue',
                ],
                [
                    'tag' => 'Control',
                    'title' => 'Full access. Real commands.',
                    'desc' => 'Run any WP-CLI command, check logs, manage databases — across every site, from one dashboard.',
                    'color' => 'violet',
                ],
            ];
            @endphp
            @foreach($cards as $card)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-6 flex flex-col gap-4 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-300">
                @php
                $colors = [
                    'emerald' => 'border-emerald-900 bg-emerald-950 text-emerald-300',
                    'blue' => 'border-blue-900 bg-blue-950 text-blue-300',
                    'violet' => 'border-violet-900 bg-violet-950 text-violet-300',
                ];
                @endphp
                <div class="inline-flex w-fit items-center px-2.5 py-1 rounded-md border text-xs font-mono {{ $colors[$card['color']] }}">{{ $card['tag'] }}</div>
                <h3 class="text-lg font-medium text-white leading-snug">{{ $card['title'] }}</h3>
                <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $card['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== FEATURE SECTIONS ===================== --}}

{{-- Feature 1: Monitoring --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">Monitoring</div>
                <h2 class="text-3xl md:text-4xl font-medium text-white tracking-tight text-balance leading-tight">
                    Know before your client does
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Uptime, SSL, domain expiry, PageSpeed regressions — monitored 24/7 across your entire portfolio.
                    When something breaks, you're the first to know.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach(['Uptime monitoring with instant alerts', 'SSL & domain expiry warnings', 'Google PageSpeed scores (mobile & desktop)', 'Vulnerability scanning for plugins & themes', 'Automated daily regression checks'] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
            {{-- Mock monitoring card --}}
            <div class="rounded-xl border border-white/10 bg-neutral-900 p-6 space-y-3">
                @foreach([
                    ['name' => 'client-site.com', 'status' => 'Operational', 'uptime' => '99.98%', 'color' => 'emerald'],
                    ['name' => 'agency-portfolio.net', 'status' => 'Operational', 'uptime' => '100%', 'color' => 'emerald'],
                    ['name' => 'shop.example.com', 'status' => 'SSL Warning', 'uptime' => '99.71%', 'color' => 'amber'],
                    ['name' => 'staging.project.io', 'status' => 'Operational', 'uptime' => '99.90%', 'color' => 'emerald'],
                    ['name' => 'legacy-blog.org', 'status' => 'Down', 'uptime' => '94.20%', 'color' => 'rose'],
                ] as $site)
                @php
                $dot = ['emerald' => 'bg-emerald-500', 'amber' => 'bg-amber-400', 'rose' => 'bg-rose-500'][$site['color']];
                $text = ['emerald' => 'text-emerald-400', 'amber' => 'text-amber-400', 'rose' => 'text-rose-400'][$site['color']];
                @endphp
                <div class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.02] px-4 py-3 hover:bg-white/[0.05] transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
                        <span class="text-sm text-neutral-200 font-mono">{{ $site['name'] }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-mono text-neutral-500">{{ $site['uptime'] }}</span>
                        <span class="text-xs {{ $text }}">{{ $site['status'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Feature 2: Git Deployments --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            {{-- Mock deploy card --}}
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">git push origin main</span>
                </div>
                <div class="p-6 font-mono text-xs space-y-2">
                    <div class="text-neutral-500">→ Webhook received from GitHub</div>
                    <div class="text-blue-400">→ Pulling latest commit <span class="text-neutral-300">a3f9c12</span></div>
                    <div class="text-neutral-400">→ Running composer install...</div>
                    <div class="text-neutral-400">→ Running npm run build...</div>
                    <div class="text-neutral-400">→ Clearing Laravel cache...</div>
                    <div class="text-emerald-400">✓ Deployed to client-site.com in 18s</div>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <div class="text-neutral-500 mb-2">Connected repositories</div>
                        @foreach([
                            ['repo' => 'github/acme/theme', 'branch' => 'main', 'sites' => 3],
                            ['repo' => 'gitlab/agency/plugin', 'branch' => 'production', 'sites' => 7],
                            ['repo' => 'bitbucket/client/wp', 'branch' => 'main', 'sites' => 1],
                        ] as $r)
                        <div class="flex justify-between items-center py-2 border-b border-white/5 last:border-0">
                            <span class="text-neutral-300">{{ $r['repo'] }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-neutral-500">{{ $r['branch'] }}</span>
                                <span class="text-xs text-neutral-600">{{ $r['sites'] }} site{{ $r['sites'] > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-violet-900 bg-violet-950 text-violet-300 text-xs font-mono mb-6">Git Deployments</div>
                <h2 class="text-3xl md:text-4xl font-medium text-white tracking-tight text-balance leading-tight">
                    Push to deploy. Every site.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Connect GitHub, GitLab, or Bitbucket and deploy on push — or on demand.
                    One repo can power dozens of sites simultaneously.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach(['GitHub, GitLab & Bitbucket supported', 'Webhook-triggered auto deployments', 'One repo across multiple sites', 'Instant deploy feedback', 'Rollback to any previous commit'] as $f)
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

{{-- Feature 3: AI Agent Mode --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0d1220 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">AI Agent Mode</div>
                <h2 class="text-3xl md:text-4xl font-medium text-white tracking-tight text-balance leading-tight">
                    An AI that knows your sites inside out.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Ask anything. The AI knows your WordPress version, active plugins, server config, and performance history.
                    It diagnoses issues, recommends fixes, and can run WP-CLI commands directly to resolve them.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach(['Full site context — plugins, PHP, server', 'Diagnoses conflicts & vulnerabilities', 'PageSpeed optimisation advice', 'Runs SSH commands to investigate', 'Available 24/7 across all sites'] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
            {{-- Mock AI chat card --}}
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <div class="h-2 w-2 rounded-full bg-blue-400 animate-pulse"></div>
                    <span class="text-xs text-neutral-400 font-medium">WPGrip AI Agent — client-site.com</span>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 h-7 w-7 rounded-full bg-neutral-700 flex items-center justify-center text-xs text-neutral-300">U</div>
                        <div class="bg-white/5 rounded-xl rounded-tl-sm px-4 py-3 text-sm text-neutral-300 leading-relaxed">
                            Why is my PageSpeed score dropping on mobile?
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <div class="bg-blue-600/20 border border-blue-500/20 rounded-xl rounded-tr-sm px-4 py-3 text-sm text-blue-100 leading-relaxed max-w-xs">
                            I can see your score dropped from 78 to 61 over the past 7 days. The Yoast SEO update (v21.9) added 48kB of undeferred JS. I recommend deferring <code class="text-blue-300">seo-schema.js</code> or rolling back to v21.8. Want me to check the file?
                        </div>
                        <div class="flex-shrink-0 h-7 w-7 rounded-full bg-blue-600 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 h-7 w-7 rounded-full bg-neutral-700 flex items-center justify-center text-xs text-neutral-300">U</div>
                        <div class="bg-white/5 rounded-xl rounded-tl-sm px-4 py-3 text-sm text-neutral-300 leading-relaxed">
                            Yes, check the file size.
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <div class="bg-blue-600/20 border border-blue-500/20 rounded-xl rounded-tr-sm px-4 py-3 text-sm text-blue-100 leading-relaxed max-w-xs">
                            <div class="text-xs text-blue-400 font-mono mb-2">$ wc -c wp-content/plugins/wordpress-seo/seo-schema.js</div>
                            <div class="font-mono text-xs text-neutral-300">49,152 bytes</div>
                            <div class="mt-2">Confirmed — 48kB unminified. Rolling back is the safest fix. Should I create a deployment task?
                            </div>
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

{{-- ===================== FEATURE GRID ===================== --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">All Features</div>
            <h2 class="text-3xl md:text-4xl font-medium text-white tracking-tight">Everything you need. Nothing you don't.</h2>
            <p class="mt-4 text-neutral-400 font-light max-w-xl mx-auto">Every plan includes the full feature set. No paywalls, no artificial limits.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
            $features = [
                ['icon' => '📡', 'title' => 'Uptime Monitoring', 'desc' => 'Instant alerts when a site goes down — before your client notices.'],
                ['icon' => '🔒', 'title' => 'SSL & Domain Tracking', 'desc' => 'Automated expiry warnings weeks in advance. Never get caught out.'],
                ['icon' => '⚡', 'title' => 'Performance Scores', 'desc' => 'Google PageSpeed tracking for mobile and desktop, with full history.'],
                ['icon' => '🛡️', 'title' => 'Vulnerability Scanning', 'desc' => 'Continuously updated CVE database flags vulnerable plugins, themes, and WP core.'],
                ['icon' => '🤖', 'title' => 'AI Agent Mode', 'desc' => 'Powered by Claude. Knows your site context and can run commands to investigate.'],
                ['icon' => '🚀', 'title' => 'Git Deployments', 'desc' => 'GitHub, GitLab, Bitbucket. Deploy on push, webhook, or on demand.'],
                ['icon' => '🗄️', 'title' => 'Database Backups', 'desc' => 'Scheduled encrypted backups. One-click restore.'],
                ['icon' => '👥', 'title' => 'Team Collaboration', 'desc' => 'Invite team members with role-based access across workspaces.'],
                ['icon' => '🔄', 'title' => 'One-Click Updates', 'desc' => 'Update plugins, themes, and WP core across all sites at once.'],
            ];
            @endphp
            @foreach($features as $f)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-6 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-200">
                <div class="text-2xl mb-4">{{ $f['icon'] }}</div>
                <h3 class="text-sm font-medium text-white mb-2">{{ $f['title'] }}</h3>
                <p class="text-sm text-neutral-500 font-light leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== FAQ ===================== --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f0f0f 100%)">
    <div class="max-w-3xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-medium text-white tracking-tight">Common questions</h2>
        </div>
        <div class="space-y-3">
            @php
            $faqs = [
                ['q' => 'Do I need to install a plugin on my sites?', 'a' => 'No. WPGrip connects via SSH using a key you add to your server. No plugins, no REST API, no third-party code on your site.'],
                ['q' => 'Which hosting providers are supported?', 'a' => 'Any host that allows SSH and has WP-CLI installed — VPS, cloud, or managed. DigitalOcean, Hetzner, AWS, Kinsta, Cloudways, WP Engine, and more.'],
                ['q' => 'What are the minimum requirements?', 'a' => 'SSH access and WP-CLI on the server. That\'s it.'],
                ['q' => 'Can I manage sites across different hosts?', 'a' => 'Yes. WPGrip is hosting-agnostic — mix and match any providers in one dashboard.'],
                ['q' => 'How are my credentials kept secure?', 'a' => 'SSH key-based auth only — no passwords stored. Keys are encrypted at rest. Your WordPress admin credentials are never needed or stored.'],
                ['q' => 'Can I use WPGrip without SSH access?', 'a' => 'Monitoring features (uptime, SSL, domain, PageSpeed) work without SSH. Management features — updates, deployments, AI agent, backups — require SSH.'],
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

{{-- ===================== CTA ===================== --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(30,64,175,0.15) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-32 text-center">
        <div class="inline-flex mb-2 mx-auto border border-white/10 bg-[#101010] rounded-none">
            <div class="flex h-8 items-center gap-2 px-4 border-b border-white/5">
                <span class="h-2 w-2 rounded-full bg-rose-400"></span>
                <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            </div>
        </div>
        <div class="border border-white/10 bg-[#101010] px-8 py-12">
            <h2 class="text-3xl md:text-4xl font-medium text-white tracking-tight text-balance">
                Your WordPress portfolio, fully under control.
            </h2>
            <p class="mt-3 text-lg text-neutral-400 font-light">Free to start. No credit card required.</p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
                <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-blue-500 bg-blue-600 text-blue-50 hover:bg-blue-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-blue-900/40">
                    Get started for free
                </a>
                <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                    View pricing
                </a>
            </div>
        </div>
    </div>
</section>



</x-layouts.app>
