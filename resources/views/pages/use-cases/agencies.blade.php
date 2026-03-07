<x-layouts.app>
<x-slot name="title">WordPress Management for Agencies — Manage Every Client Site From One Dashboard</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(37,99,235,0.35) 0%, rgba(37,99,235,0.10) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
            For Agencies
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            50 client sites.<br><span class="text-blue-400">One dashboard.</span>
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            Managing WordPress sites across multiple clients, servers, and hosting providers is chaotic without the right tool. WPGrip gives your team a single pane of glass for monitoring, updates, deployments, and database backups — without installing plugins on client sites.
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

{{-- PAIN POINTS --}}
<section class="py-24 bg-[#0a0a0a]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">The problem</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                Sound familiar?
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
            $pains = [
                ['title' => 'Logging into 30 different WordPress admins', 'desc' => 'Every client has a different URL, different credentials, different hosting. You spend half your morning just navigating between admin panels to check for updates.'],
                ['title' => 'A client emails: "my site is down"', 'desc' => 'You had no idea. There was no alert. Now you\'re scrambling to diagnose while the client is frustrated. Downtime was 45 minutes before anyone noticed.'],
                ['title' => 'Deploying the same theme to 12 sites', 'desc' => 'You updated the agency starter theme and now need to push it to every site that uses it. That\'s 12 separate SFTP uploads or manual git pulls.'],
            ];
            @endphp
            @foreach($pains as $p)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-7 flex flex-col gap-4">
                <h3 class="text-lg font-medium text-white leading-snug">{{ $p['title'] }}</h3>
                <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $p['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SOLUTION --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">WPGrip for agencies</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                Everything your agency needs. Nothing your clients need to worry about.
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                WPGrip connects to your clients' servers via SSH. No plugins to install, no credentials to store in WordPress, no code running on their sites. Your clients never know it's there — they just notice their site never goes down.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
            $features = [
                ['icon' => '📊', 'title' => 'Portfolio dashboard', 'desc' => 'See uptime, SSL status, PHP/WP versions, disk usage, and vulnerabilities for every site on one screen. Filter by client or server.'],
                ['icon' => '🔔', 'title' => 'Uptime and SSL alerts', 'desc' => 'Get Slack or email alerts the moment a site goes down or an SSL certificate is about to expire. Act before your client notices.'],
                ['icon' => '🚀', 'title' => 'Git deployments', 'desc' => 'Push your agency theme or plugin to GitHub and it deploys across every connected site. One repo, many sites.'],
                ['icon' => '🛡️', 'title' => 'Vulnerability scanning', 'desc' => 'Every plugin and theme checked against the WPScan database daily. Know which client sites have vulnerable components before they get exploited.'],
                ['icon' => '🗄️', 'title' => 'Automated DB backups', 'desc' => 'Encrypted database backups on a schedule. One-click restore to any point. File backups are your hosting provider\'s responsibility — we protect the data that matters most.'],
                ['icon' => '👥', 'title' => 'Team collaboration', 'desc' => 'Invite your team members to the workspace. Everyone sees the same dashboard, same alerts, same deployment logs.'],
            ];
            @endphp
            @foreach($features as $f)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-6 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-200 flex flex-col gap-3">
                <span class="text-2xl">{{ $f['icon'] }}</span>
                <h3 class="text-sm font-medium text-white">{{ $f['title'] }}</h3>
                <p class="text-sm text-neutral-500 font-light leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- NO PLUGIN ADVANTAGE --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">No plugin required</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Your clients' sites stay clean
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Other management tools require a plugin on every WordPress site. That plugin consumes server resources, needs updating, and adds an attack surface. Some clients don't want third-party plugins on their site. With WPGrip, there's nothing to install, nothing to explain, nothing to maintain on the WordPress side.
                </p>
            </div>
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">client-shop.com · plugins</span>
                </div>
                <div class="p-5 font-mono text-xs space-y-2">
                    <div class="text-neutral-500"># Active plugins (via WP-CLI over SSH)</div>
                    <div class="text-emerald-400">$ wp plugin list --status=active --format=table</div>
                    <div class="text-neutral-300 mt-2">
                        <div class="grid grid-cols-3 gap-4 text-neutral-500 border-b border-white/5 pb-2 mb-2">
                            <span>name</span><span>version</span><span>status</span>
                        </div>
                        <div class="grid grid-cols-3 gap-4"><span>woocommerce</span><span>8.5.2</span><span class="text-emerald-400">active</span></div>
                        <div class="grid grid-cols-3 gap-4"><span>yoast-seo</span><span>21.9</span><span class="text-emerald-400">active</span></div>
                        <div class="grid grid-cols-3 gap-4"><span>contact-form-7</span><span>5.8.4</span><span class="text-emerald-400">active</span></div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-white/5 text-neutral-600">
                        # No management plugin needed ✓
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
            Manage every client site from one place
        </h2>
        <p class="mt-3 text-lg text-neutral-400 font-light">Free trial. No credit card. No plugins on client sites.</p>
        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-blue-500 bg-blue-600 text-blue-50 hover:bg-blue-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-blue-900/40">
                Start your free trial
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                View pricing
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
