<x-layouts.app>
<x-slot name="title">Git Deployments for WordPress — Push to Deploy Across Every Site</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(37,99,235,0.35) 0%, rgba(37,99,235,0.10) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
            Git Deployments
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            Push to deploy.<br>Across every site.
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            Connect your GitHub or Bitbucket repos to your WordPress sites. Push to your branch, and WPGrip pulls the code to every connected site over SSH. One repo can deploy to dozens of sites at once.
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

{{-- DEPLOYMENT LOG MOCK --}}
<section class="py-24 bg-[#0a0a0a]">
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
                    <div class="text-neutral-500">→ Webhook received · github/acme/starter-theme · push main</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;Commit: <span class="text-neutral-300">a3f9c12</span> <span class="text-neutral-500">"Fix header spacing on mobile"</span></div>
                    <div class="text-neutral-400">&nbsp;&nbsp;Author: <span class="text-neutral-300">sarah@agency.com</span></div>
                    <div class="mt-3 text-blue-400">→ Deploying to 3 connected sites...</div>
                    <div class="mt-2 text-neutral-500">&nbsp;&nbsp;[1/3] client-site.com</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;&nbsp;&nbsp;→ SSH connected</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;&nbsp;&nbsp;→ git pull origin main</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;&nbsp;&nbsp;→ wp cache flush</div>
                    <div class="text-emerald-400">&nbsp;&nbsp;&nbsp;&nbsp;✓ Deployed in 4s</div>
                    <div class="mt-1 text-neutral-500">&nbsp;&nbsp;[2/3] agency-portfolio.net</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;&nbsp;&nbsp;→ SSH connected</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;&nbsp;&nbsp;→ git pull origin main</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;&nbsp;&nbsp;→ wp cache flush</div>
                    <div class="text-emerald-400">&nbsp;&nbsp;&nbsp;&nbsp;✓ Deployed in 6s</div>
                    <div class="mt-1 text-neutral-500">&nbsp;&nbsp;[3/3] shop.example.com</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;&nbsp;&nbsp;→ SSH connected</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;&nbsp;&nbsp;→ git pull origin main</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;&nbsp;&nbsp;→ wp cache flush</div>
                    <div class="text-emerald-400">&nbsp;&nbsp;&nbsp;&nbsp;✓ Deployed in 5s</div>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <div class="text-emerald-400">✓ All 3 sites deployed in 15s</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-violet-900 bg-violet-950 text-violet-300 text-xs font-mono mb-6">Push to Deploy</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Push your code. WPGrip does the rest.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    When you push to your tracked branch, a webhook fires. WPGrip receives it, connects to every site linked to that repo over SSH, and pulls the latest code. You see the full deployment log in real time.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Webhook-triggered deployments on push',
                        'Full real-time deployment log',
                        'Deploy to one site or many at once',
                        'SSH-based git pull — no FTP, no file uploads',
                        'Automatic WP cache flush after deploy',
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

{{-- HOW PUSH-TO-DEPLOY WORKS --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">How It Works</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                From git push to live site in seconds
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                Connect a repo, pick a branch, link your sites. Every push to that branch triggers a deployment to every connected site. No CI/CD pipeline to configure. No build server to maintain.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
            $steps = [
                ['step' => '01', 'title' => 'Connect your repo', 'desc' => 'Link your GitHub or Bitbucket repository. WPGrip sets up the webhook automatically.'],
                ['step' => '02', 'title' => 'Pick your branch', 'desc' => 'Choose which branch to track — main, production, staging. Different sites can follow different branches.'],
                ['step' => '03', 'title' => 'Link your sites', 'desc' => 'Connect one or more WordPress sites to the repo. A theme repo can power 20 client sites at once.'],
                ['step' => '04', 'title' => 'Push and deploy', 'desc' => 'Push to your branch. The webhook fires. WPGrip pulls the code to every linked site over SSH.'],
            ];
            @endphp
            @foreach($steps as $s)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-7 flex flex-col gap-4 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-300">
                <div class="inline-flex w-fit items-center px-2.5 py-1 rounded-md border border-violet-900 bg-violet-950 text-violet-300 text-xs font-mono">Step {{ $s['step'] }}</div>
                <h3 class="text-lg font-medium text-white leading-snug">{{ $s['title'] }}</h3>
                <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- MULTI-SITE REPOS --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">Shared Repos</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    One repo. Many sites.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    If you build a starter theme or a custom plugin that runs on multiple client sites, you don't want to deploy to each site manually. Link the repo once, connect every site that uses it, and every push deploys everywhere.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Connect a single repo to unlimited sites',
                        'Deploy a shared theme across your entire client base',
                        'Push a plugin update to every site that uses it',
                        'Each site pulls independently — one failure doesn\'t block others',
                        'See per-site deploy status in the log',
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
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">shared repositories</span>
                </div>
                <div class="p-6 space-y-4">
                    @foreach([
                        ['repo' => 'github / acme / starter-theme', 'branch' => 'main', 'sites' => ['client-site.com', 'agency-portfolio.net', 'shop.example.com']],
                        ['repo' => 'gitlab / agency / core-plugin', 'branch' => 'production', 'sites' => ['client-site.com', 'shop.example.com', 'legacy-blog.org', 'staging.project.io', 'new-build.dev', 'partner-site.co', 'demo.agency.com']],
                        ['repo' => 'bitbucket / client / wp-config', 'branch' => 'main', 'sites' => ['staging.project.io']],
                    ] as $repo)
                    <div class="rounded-lg border border-white/5 bg-white/[0.02] px-4 py-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm text-neutral-200 font-mono">{{ $repo['repo'] }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded border border-violet-900 bg-violet-950 text-violet-300 text-xs font-mono">{{ $repo['branch'] }}</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($repo['sites'] as $site)
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-white/5 border border-white/10 text-xs text-neutral-400 font-mono">{{ $site }}</span>
                            @endforeach
                        </div>
                        <div class="mt-3 text-xs text-neutral-500">{{ count($repo['sites']) }} site{{ count($repo['sites']) > 1 ? 's' : '' }} connected</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- BRANCH MANAGEMENT --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">branch → site mapping</span>
                </div>
                <div class="p-6 space-y-3">
                    <div class="text-xs text-neutral-500 font-mono uppercase tracking-wide mb-4">github / acme / starter-theme</div>
                    @foreach([
                        ['branch' => 'main',        'sites' => 'client-site.com, agency-portfolio.net', 'env' => 'Production', 'c' => 'emerald'],
                        ['branch' => 'staging',     'sites' => 'staging.project.io',                     'env' => 'Staging',    'c' => 'amber'],
                        ['branch' => 'development', 'sites' => 'local.dev.test',                         'env' => 'Development','c' => 'blue'],
                    ] as $b)
                    @php
                    $envColor = ['emerald'=>'border-emerald-900 bg-emerald-950 text-emerald-300','amber'=>'border-amber-900 bg-amber-950 text-amber-300','blue'=>'border-blue-900 bg-blue-950 text-blue-300'][$b['c']];
                    @endphp
                    <div class="rounded-lg border border-white/5 bg-white/[0.02] px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="h-4 w-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span class="text-sm text-neutral-200 font-mono">{{ $b['branch'] }}</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded border text-xs font-mono {{ $envColor }}">{{ $b['env'] }}</span>
                        </div>
                        <div class="mt-2 text-xs text-neutral-500 font-mono">→ {{ $b['sites'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">Branch Management</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Different branches for different environments
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Map branches to environments. Your production sites follow the main branch. Staging sites track the staging branch. Development sites pull from dev. Each push only deploys to the sites following that branch.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Map any branch to any set of sites',
                        'Production, staging, and dev environments from one repo',
                        'Push to staging without touching production',
                        'Promote code by merging branches — deploys follow',
                        'Branch-level control for safe, staged rollouts',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ROLLBACK --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono mb-6">Rollback</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Deployed a bug? Roll back to any commit.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Every deployment is logged with its commit hash. If a deploy breaks something, you can roll back to any previous commit from the dashboard. WPGrip checks out the specified commit on the server over SSH — your site is back to the working version.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Roll back to any previous commit',
                        'One-click rollback from the deployment history',
                        'Rollback deploys across all connected sites',
                        'Full log of what was rolled back and when',
                        'No git knowledge needed — pick the commit, click rollback',
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
                    <span class="ml-3 text-xs text-neutral-500 font-mono">rollback · client-site.com</span>
                </div>
                <div class="p-6 font-mono text-xs space-y-2">
                    <div class="text-rose-400">✗ Deploy failed — site returned 500 after deploy</div>
                    <div class="text-neutral-500">&nbsp;&nbsp;Commit: <span class="text-neutral-300">b7e2d41</span> <span class="text-neutral-500">"Refactor query logic"</span></div>
                    <div class="mt-3 text-neutral-500">→ Rollback requested to <span class="text-neutral-300">a3f9c12</span></div>
                    <div class="text-neutral-400">→ SSH connected to client-site.com</div>
                    <div class="text-neutral-400">→ git checkout a3f9c12</div>
                    <div class="text-neutral-400">→ wp cache flush</div>
                    <div class="text-emerald-400">✓ Rolled back to a3f9c12</div>
                    <div class="text-emerald-400">✓ Site returning 200</div>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <div class="text-emerald-400">✓ Rollback complete in 3s</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- DEPLOYMENT HISTORY --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">deployment history</span>
                </div>
                <div class="p-6 space-y-2">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs text-neutral-500 font-mono uppercase tracking-wide">Recent Deployments</span>
                        <span class="text-xs text-neutral-500 font-mono">client-site.com</span>
                    </div>
                    @foreach([
                        ['commit' => 'a3f9c12', 'msg' => 'Fix header spacing on mobile', 'time' => '2 min ago',  'author' => 'sarah', 'status' => 'Deployed',  'c' => 'emerald'],
                        ['commit' => 'b7e2d41', 'msg' => 'Refactor query logic',          'time' => '18 min ago', 'author' => 'sarah', 'status' => 'Rolled back', 'c' => 'rose'],
                        ['commit' => 'c91fa08', 'msg' => 'Add testimonials block',        'time' => '2 hours ago','author' => 'james', 'status' => 'Deployed',  'c' => 'emerald'],
                        ['commit' => 'd4b3e77', 'msg' => 'Update footer nav links',       'time' => '5 hours ago','author' => 'sarah', 'status' => 'Deployed',  'c' => 'emerald'],
                        ['commit' => 'e28cc91', 'msg' => 'Bump plugin version to 2.1',    'time' => 'Yesterday',  'author' => 'james', 'status' => 'Deployed',  'c' => 'emerald'],
                    ] as $deploy)
                    @php
                    $dot = ['emerald'=>'bg-emerald-500','rose'=>'bg-rose-500'][$deploy['c']];
                    $txt = ['emerald'=>'text-emerald-400','rose'=>'text-rose-400'][$deploy['c']];
                    @endphp
                    <div class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.02] px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-neutral-300 font-mono">{{ $deploy['commit'] }}</span>
                                    <span class="text-xs text-neutral-500 truncate max-w-[180px]">{{ $deploy['msg'] }}</span>
                                </div>
                                <span class="text-xs text-neutral-600">{{ $deploy['author'] }} · {{ $deploy['time'] }}</span>
                            </div>
                        </div>
                        <span class="text-xs {{ $txt }} font-mono whitespace-nowrap">{{ $deploy['status'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">Deployment History</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    A complete record of every deploy
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Every deployment is logged with the commit hash, message, author, timestamp, and outcome. You know exactly what code is running on every site, when it was deployed, and by whom. If something goes wrong, you have the full trail.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Full history of every deployment per site',
                        'Commit hash, message, and author logged',
                        'Timestamps for every deploy and rollback',
                        'Status tracking — deployed, failed, rolled back',
                        'Filter by site, repo, or date range',
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

{{-- CTA --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(30,64,175,0.15) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-28 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight text-balance">
            Ship code without touching a server
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
