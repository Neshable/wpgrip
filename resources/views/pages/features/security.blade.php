<x-layouts.app>
<x-slot name="title">WordPress Security Scanning — Vulnerability Detection Without Plugins</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(37,99,235,0.35) 0%, rgba(37,99,235,0.10) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
            Security
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            Find vulnerabilities.<br>Without adding one.
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            WPGrip scans your plugins, themes, and WordPress core against a known vulnerability database. It runs over SSH using WP-CLI — no security plugin installed on your site, no extra attack surface.
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

{{-- VULNERABILITY SCAN RESULTS MOCK --}}
<section class="py-24 bg-[#0a0a0a]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono mb-6">Scan Results</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    See what's vulnerable across every site
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    WPGrip checks the exact version of every plugin, theme, and WordPress core installation against the WPScan vulnerability database. You get a clear list of what's affected, the severity, and which version fixes it.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Scans plugins, themes, and WordPress core versions',
                        'Matches against the WPScan vulnerability database',
                        'Shows severity level for each vulnerability',
                        'Tells you which version contains the fix',
                        'Flags vulnerabilities across your entire portfolio',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-rose-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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
                    <span class="ml-3 text-xs text-neutral-500 font-mono">vulnerability scan · client-site.com</span>
                </div>
                <div class="p-6 space-y-2">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs text-neutral-500 font-mono uppercase tracking-wide">Scan Results</span>
                        <span class="text-xs text-neutral-500 font-mono">Last scan: 4 hours ago</span>
                    </div>
                    @foreach([
                        ['type' => 'Plugin', 'name' => 'Contact Form 7', 'version' => '5.7.1', 'severity' => 'High', 'fix' => '5.7.2', 'vuln' => 'Stored XSS via form fields', 'c' => 'rose'],
                        ['type' => 'Plugin', 'name' => 'WooCommerce',    'version' => '8.3.0', 'severity' => 'Medium', 'fix' => '8.3.1', 'vuln' => 'CSRF in coupon endpoint',    'c' => 'amber'],
                        ['type' => 'Theme',  'name' => 'flavor starter',     'version' => '2.1.4', 'severity' => 'Low',    'fix' => '2.1.5', 'vuln' => 'Information disclosure via debug output', 'c' => 'yellow'],
                    ] as $vuln)
                    @php
                    $sevColor = ['rose'=>'border-rose-900 bg-rose-950 text-rose-300','amber'=>'border-amber-900 bg-amber-950 text-amber-300','yellow'=>'border-yellow-900 bg-yellow-950 text-yellow-300'][$vuln['c']];
                    @endphp
                    <div class="rounded-lg border border-white/5 bg-white/[0.02] px-4 py-3">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-neutral-500 font-mono">{{ $vuln['type'] }}</span>
                                <span class="text-sm text-neutral-200 font-mono">{{ $vuln['name'] }}</span>
                                <span class="text-xs text-neutral-600 font-mono">v{{ $vuln['version'] }}</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded border text-xs font-mono {{ $sevColor }}">{{ $vuln['severity'] }}</span>
                        </div>
                        <div class="text-xs text-neutral-400">{{ $vuln['vuln'] }}</div>
                        <div class="text-xs text-neutral-500 mt-1">Fixed in <span class="text-emerald-400">v{{ $vuln['fix'] }}</span></div>
                    </div>
                    @endforeach

                    {{-- Clean items --}}
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <div class="text-xs text-neutral-500 font-mono uppercase tracking-wide mb-3">No Issues Found</div>
                        @foreach([
                            ['type' => 'Core',   'name' => 'WordPress', 'version' => '6.5.2'],
                            ['type' => 'Plugin', 'name' => 'Yoast SEO', 'version' => '22.4'],
                            ['type' => 'Plugin', 'name' => 'ACF Pro',   'version' => '6.2.6'],
                        ] as $clean)
                        <div class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.02] px-4 py-2 mb-1">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span class="text-xs text-neutral-500 font-mono">{{ $clean['type'] }}</span>
                                <span class="text-sm text-neutral-300 font-mono">{{ $clean['name'] }}</span>
                                <span class="text-xs text-neutral-600 font-mono">v{{ $clean['version'] }}</span>
                            </div>
                            <span class="text-xs text-emerald-400 font-mono">Clean</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- HOW SCANNING WORKS --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">How It Works</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                SSH in. Read versions. Check the database.
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                WPGrip connects to your server over SSH and uses WP-CLI to list every installed plugin, theme, and the WordPress core version. It then checks each version against the WPScan vulnerability database. No code runs on your WordPress site. No plugin is installed. Nothing is exposed to the web.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
            $steps = [
                ['step' => '01', 'title' => 'Inventory via WP-CLI', 'desc' => 'WPGrip runs wp plugin list and wp theme list over SSH to get the exact name and version of everything installed on your site.'],
                ['step' => '02', 'title' => 'Match against the database', 'desc' => 'Each plugin, theme, and core version is checked against the WPScan vulnerability database — a continuously updated catalog of known WordPress security issues.'],
                ['step' => '03', 'title' => 'Report and alert', 'desc' => 'If a vulnerability is found, you see it in your dashboard with the severity, description, and the version that fixes it. Alerts go to your configured channels.'],
            ];
            @endphp
            @foreach($steps as $s)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-7 flex flex-col gap-4 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-300">
                <div class="inline-flex w-fit items-center px-2.5 py-1 rounded-md border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono">Step {{ $s['step'] }}</div>
                <h3 class="text-lg font-medium text-white leading-snug">{{ $s['title'] }}</h3>
                <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- VULNERABILITY DATABASE --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">vulnerability database</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="rounded-lg border border-white/5 bg-white/[0.02] p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                            <span class="text-sm text-neutral-200 font-medium">WPScan Vulnerability Database</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded border border-white/5 bg-white/[0.02] p-3">
                                <div class="text-xs text-neutral-500">Plugins tracked</div>
                                <div class="text-lg text-white font-mono">60,000+</div>
                            </div>
                            <div class="rounded border border-white/5 bg-white/[0.02] p-3">
                                <div class="text-xs text-neutral-500">Known vulnerabilities</div>
                                <div class="text-lg text-white font-mono">50,000+</div>
                            </div>
                            <div class="rounded border border-white/5 bg-white/[0.02] p-3">
                                <div class="text-xs text-neutral-500">Themes tracked</div>
                                <div class="text-lg text-white font-mono">10,000+</div>
                            </div>
                            <div class="rounded border border-white/5 bg-white/[0.02] p-3">
                                <div class="text-xs text-neutral-500">Updated</div>
                                <div class="text-lg text-white font-mono">Daily</div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg border border-white/5 bg-white/[0.02] p-4">
                        <div class="text-xs text-neutral-500 font-mono uppercase tracking-wide mb-3">Recent Additions</div>
                        @foreach([
                            ['name' => 'CF7 Stored XSS',            'date' => 'May 26, 2025', 'severity' => 'High'],
                            ['name' => 'WooCommerce CSRF',           'date' => 'May 25, 2025', 'severity' => 'Medium'],
                            ['name' => 'Elementor Path Traversal',   'date' => 'May 24, 2025', 'severity' => 'High'],
                            ['name' => 'Jetpack Info Disclosure',    'date' => 'May 23, 2025', 'severity' => 'Low'],
                        ] as $entry)
                        @php
                        $sevColor = ['High'=>'text-rose-400','Medium'=>'text-amber-400','Low'=>'text-yellow-400'][$entry['severity']];
                        @endphp
                        <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                            <span class="text-xs text-neutral-300 font-mono">{{ $entry['name'] }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-neutral-600 font-mono">{{ $entry['date'] }}</span>
                                <span class="text-xs font-mono {{ $sevColor }}">{{ $entry['severity'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">Vulnerability Database</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Backed by the WPScan database
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    The WPScan vulnerability database is the most comprehensive catalog of WordPress security issues. It covers tens of thousands of plugins, themes, and WordPress core versions. WPGrip checks your sites against this database so you know about vulnerabilities as they're disclosed.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Covers plugins, themes, and WordPress core',
                        'Updated daily with newly disclosed vulnerabilities',
                        'Includes severity ratings and fix versions',
                        'Tracks the most widely used WordPress ecosystem',
                        'Trusted by security teams and hosting providers',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- WHAT GETS CHECKED --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-amber-900 bg-amber-950 text-amber-300 text-xs font-mono mb-6">What Gets Checked</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Every plugin. Every theme. Every version.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    WPGrip doesn't just check active plugins. It checks everything installed on your site — active or inactive. An inactive plugin with a vulnerability is still a risk if the files are on the server.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Active and inactive plugins',
                        'Active and inactive themes',
                        'WordPress core version',
                        'Exact version matching — not guesswork',
                        'Cross-referenced against known CVEs',
                        'Severity classification: High, Medium, Low',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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
                    <span class="ml-3 text-xs text-neutral-500 font-mono">scan scope · client-site.com</span>
                </div>
                <div class="p-6 font-mono text-xs space-y-2">
                    <div class="text-neutral-500">→ Connecting to client-site.com via SSH...</div>
                    <div class="text-emerald-400">✓ Connected</div>
                    <div class="mt-2 text-neutral-500">→ wp core version</div>
                    <div class="text-neutral-300">&nbsp;&nbsp;WordPress 6.5.2</div>
                    <div class="text-emerald-400">&nbsp;&nbsp;✓ No known vulnerabilities</div>
                    <div class="mt-2 text-neutral-500">→ wp plugin list --format=json</div>
                    <div class="text-neutral-300">&nbsp;&nbsp;12 plugins found (9 active, 3 inactive)</div>
                    <div class="text-emerald-400">&nbsp;&nbsp;✓ 10 clean</div>
                    <div class="text-rose-400">&nbsp;&nbsp;✗ 1 vulnerability (Contact Form 7 v5.7.1)</div>
                    <div class="text-amber-400">&nbsp;&nbsp;✗ 1 vulnerability (WooCommerce v8.3.0)</div>
                    <div class="mt-2 text-neutral-500">→ wp theme list --format=json</div>
                    <div class="text-neutral-300">&nbsp;&nbsp;3 themes found (1 active, 2 inactive)</div>
                    <div class="text-emerald-400">&nbsp;&nbsp;✓ 2 clean</div>
                    <div class="text-yellow-400">&nbsp;&nbsp;✗ 1 vulnerability (flavor starter v2.1.4)</div>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <div class="text-neutral-400">→ Summary: 3 vulnerabilities found across 15 components</div>
                        <div class="text-neutral-500">&nbsp;&nbsp;High: 1 · Medium: 1 · Low: 1</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ZERO FOOTPRINT --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="space-y-4">
                <div class="rounded-xl border border-white/5 bg-white/[0.02] p-6">
                    <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono mb-4">Security plugin</div>
                    <ul class="space-y-2">
                        @foreach([
                            'Installs PHP code on your WordPress site',
                            'Adds REST API endpoints that attackers can probe',
                            'Stores configuration in your database',
                            'Runs on every page load — adds latency',
                            'Requires WordPress admin credentials',
                            'Is itself a potential attack vector',
                        ] as $f)
                        <li class="flex items-center gap-2 text-sm text-neutral-400">
                            <svg class="h-4 w-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-xl border border-emerald-500/20 bg-emerald-950/10 p-6">
                    <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-4">WPGrip scanning</div>
                    <ul class="space-y-2">
                        @foreach([
                            'Zero code installed on your WordPress site',
                            'No REST API endpoints exposed',
                            'Nothing stored in your WordPress database',
                            'Zero impact on page load performance',
                            'Uses SSH key authentication — no WP credentials',
                            'Cannot be exploited from the web',
                        ] as $f)
                        <li class="flex items-center gap-2 text-sm text-neutral-300">
                            <svg class="h-4 w-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">Zero Footprint</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    The irony of security plugins
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Most WordPress security plugins are themselves an attack surface. They install PHP files, create REST API endpoints, store data in your database, and run code on every page load. If the security plugin has a vulnerability — and they do — your site is exposed.
                </p>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    WPGrip takes a different approach. Your vulnerability scan runs over SSH, outside of WordPress. Nothing is installed. Nothing is exposed to the web. Nothing runs when your visitors load a page. The scan reads version numbers, checks a database, and reports back. That's it.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- PORTFOLIO-WIDE VIEW --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-violet-900 bg-violet-950 text-violet-300 text-xs font-mono mb-6">Portfolio View</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    One vulnerable plugin, ten affected sites
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    When a popular plugin discloses a vulnerability, you need to know which of your sites are affected. WPGrip gives you a portfolio-wide view — see every site running the vulnerable version, and update them all from one place.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Portfolio-wide vulnerability dashboard',
                        'See which sites are affected by a specific CVE',
                        'Update vulnerable plugins across all sites at once',
                        'Track your fix progress as you patch each site',
                        'Alerts when new vulnerabilities affect your portfolio',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-violet-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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
                    <span class="ml-3 text-xs text-neutral-500 font-mono">portfolio · affected sites</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="rounded-lg border border-rose-500/20 bg-rose-950/20 px-4 py-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-rose-300 font-mono">Contact Form 7 — Stored XSS</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono">High</span>
                        </div>
                        <div class="text-xs text-neutral-500 mb-3">Affected: v5.7.1 and below · Fixed in v5.7.2</div>
                        <div class="space-y-1.5">
                            @foreach([
                                ['site' => 'client-site.com',      'version' => '5.7.1', 'status' => 'Vulnerable', 'c' => 'rose'],
                                ['site' => 'agency-portfolio.net', 'version' => '5.7.1', 'status' => 'Vulnerable', 'c' => 'rose'],
                                ['site' => 'shop.example.com',     'version' => '5.7.2', 'status' => 'Patched',    'c' => 'emerald'],
                                ['site' => 'legacy-blog.org',      'version' => '5.7.1', 'status' => 'Vulnerable', 'c' => 'rose'],
                                ['site' => 'staging.project.io',   'version' => '5.7.2', 'status' => 'Patched',    'c' => 'emerald'],
                            ] as $affected)
                            @php
                            $statusColor = ['rose'=>'text-rose-400','emerald'=>'text-emerald-400'][$affected['c']];
                            $dot = ['rose'=>'bg-rose-500','emerald'=>'bg-emerald-500'][$affected['c']];
                            @endphp
                            <div class="flex items-center justify-between rounded bg-white/[0.02] px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
                                    <span class="text-xs text-neutral-300 font-mono">{{ $affected['site'] }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-neutral-600 font-mono">v{{ $affected['version'] }}</span>
                                    <span class="text-xs font-mono {{ $statusColor }}">{{ $affected['status'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3 text-xs text-neutral-500">3 of 5 sites still vulnerable</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(30,64,175,0.15) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-28 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight text-balance">
            Protect your sites without adding to the problem
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
