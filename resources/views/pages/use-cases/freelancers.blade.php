<x-layouts.app>
<x-slot name="title">WordPress Management for Freelancers — Stop Juggling Logins</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(124,58,237,0.30) 0%, rgba(124,58,237,0.08) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-violet-500/30 bg-violet-500/10 text-violet-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-violet-400"></span>
            For Freelancers
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            You're one person.<br><span class="text-violet-400">Manage like a team.</span>
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            You built the sites. Now you maintain them. WPGrip handles the monitoring, updates, backups, and vulnerability scanning across all your client sites — so you can focus on building, not babysitting.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-violet-500 bg-violet-600 text-violet-50 hover:bg-violet-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-violet-900/40">
                Start your free trial
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                See pricing
            </a>
        </div>
    </div>
</section>

{{-- YOUR WEEK WITHOUT WPGRIP --}}
<section class="py-24 bg-[#0a0a0a]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">The reality</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                You didn't become a freelancer to spend Monday mornings checking plugin updates.
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                But here you are — logging into 8 different WordPress admins, running updates one by one, hoping nothing breaks, and praying that client site from 2019 isn't quietly getting hacked.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-7">
                <h3 class="text-sm font-medium text-neutral-500 mb-4 uppercase tracking-wide">Without WPGrip</h3>
                <ul class="space-y-3">
                    @foreach([
                        'Log into each WordPress admin separately',
                        'Manually check for plugin and theme updates',
                        'Hope no site went down overnight',
                        'Scramble when a client reports an issue',
                        'SFTP files one site at a time',
                        'Forget which sites have outdated PHP',
                    ] as $f)
                    <li class="flex items-center gap-2 text-sm text-neutral-500">
                        <svg class="h-4 w-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-xl border border-emerald-500/20 bg-emerald-950/20 p-7">
                <h3 class="text-sm font-medium text-emerald-400 mb-4 uppercase tracking-wide">With WPGrip</h3>
                <ul class="space-y-3">
                    @foreach([
                        'One dashboard for every site',
                        'See all pending updates at a glance',
                        'Alerts before downtime becomes a problem',
                        'Know about issues before your client does',
                        'Git push to deploy across sites',
                        'PHP, WP version, and disk usage tracked',
                    ] as $f)
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

{{-- KEY FEATURES FOR FREELANCERS --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-violet-900 bg-violet-950 text-violet-300 text-xs font-mono mb-6">Built for your workflow</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                The tools you need. None of the bloat.
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
            $features = [
                ['icon' => '📱', 'title' => 'Uptime monitoring', 'desc' => 'Know the moment a client site goes down. Get alerts on Slack or email with enough detail to act.'],
                ['icon' => '🔄', 'title' => 'One-click updates', 'desc' => 'Update plugins, themes, and core across all sites from one place. WP-CLI handles it over SSH — fast and reliable.'],
                ['icon' => '🛡️', 'title' => 'Vulnerability alerts', 'desc' => 'Know which client sites have vulnerable plugins before they get exploited. Daily checks against the WPScan database.'],
                ['icon' => '🗄️', 'title' => 'Automated backups', 'desc' => 'Encrypted database backups on a schedule. Restore with one click. Peace of mind for you and your clients.'],
                ['icon' => '🚀', 'title' => 'Git deployments', 'desc' => 'Push to GitHub or Bitbucket and your changes deploy to any connected site. No more SFTP.'],
                ['icon' => '🤖', 'title' => 'AI assistant', 'desc' => 'Ask the AI about any site — it knows the stack, the plugins, the performance history. It can even run commands to investigate.'],
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

{{-- CTA --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(124,58,237,0.12) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-28 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight text-balance">
            Spend your time building. Not maintaining.
        </h2>
        <p class="mt-3 text-lg text-neutral-400 font-light">Free trial. No credit card. No plugins to install.</p>
        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-violet-500 bg-violet-600 text-violet-50 hover:bg-violet-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-violet-900/40">
                Start your free trial
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                View pricing
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
