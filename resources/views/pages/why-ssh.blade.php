<x-layouts.app>
<x-slot name="title">Why SSH — No Plugin Required for WordPress Management</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(16,185,129,0.25) 0%, rgba(16,185,129,0.08) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
            No Plugin Required
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            Why we chose SSH<br><span class="text-emerald-400">over a WordPress plugin.</span>
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            Every WordPress management tool asks you to install a plugin. We don't. Here's why that matters for your sites' speed, security, and reliability.
        </p>
    </div>
</section>

{{-- THE PLUGIN PROBLEM --}}
<section class="py-24 bg-[#0a0a0a]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono mb-6">The plugin problem</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                Every management plugin has the same flaw.
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                Tools like ManageWP, WP Umbrella, MainWP, and InfiniteWP all require a "connector" plugin installed on every WordPress site you manage. That plugin runs PHP on every single page request, exposes API endpoints, stores authentication tokens in your WordPress database, and adds another dependency you need to keep updated. That's the price of convenience — or so they say.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
            $problems = [
                ['icon' => '⚠️', 'title' => 'Performance cost', 'desc' => 'The connector plugin loads on every page request. It hooks into WordPress, checks for commands, and phones home to the management server. That\'s PHP execution time added to every visitor\'s experience.'],
                ['icon' => '🔓', 'title' => 'Exposed API endpoints', 'desc' => 'These plugins register REST API or admin-ajax endpoints that accept commands from the management service. Those endpoints are reachable from the public internet — and they\'ve been exploited before.'],
                ['icon' => '🔑', 'title' => 'Stored credentials', 'desc' => 'The plugin stores authentication tokens in the WordPress database. If someone compromises the database (SQL injection, leaked backup), they get access to your management tool\'s connection.'],
                ['icon' => '🔄', 'title' => 'Another update cycle', 'desc' => 'The connector plugin itself needs regular updates. If you forget to update it, you\'re running vulnerable code on your production sites. If you do update it, there\'s a chance it breaks something.'],
                ['icon' => '💥', 'title' => 'Fails during fatal errors', 'desc' => 'If your WordPress site has a fatal PHP error, the connector plugin can\'t run either. That means your management tool can\'t report the problem or help you fix it when you need it most.'],
                ['icon' => '🧩', 'title' => 'Plugin conflicts', 'desc' => 'WordPress plugins can conflict with each other. Security plugins may block the management plugin\'s requests. Caching plugins may serve stale responses. The connector plugin becomes another moving part.'],
            ];
            @endphp
            @foreach($problems as $p)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-6 flex flex-col gap-3">
                <span class="text-xl">{{ $p['icon'] }}</span>
                <h3 class="text-sm font-medium text-white">{{ $p['title'] }}</h3>
                <p class="text-sm text-neutral-500 font-light leading-relaxed">{{ $p['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- THE SSH APPROACH --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">The SSH approach</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    One SSH key in <code class="text-emerald-400 bg-white/5 px-2 py-0.5 rounded text-3xl">authorized_keys</code>.<br>That's it.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    WPGrip connects to your server the same way you do — via SSH. No code installed on WordPress. No endpoints exposed. No database tokens. Just a cryptographic key pair that gives WPGrip access to run WP-CLI commands on your server.
                </p>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    When we need to check for updates, we SSH in and run <code class="text-neutral-300 bg-white/5 px-1.5 py-0.5 rounded text-sm">wp plugin list</code>. When we back up your database, we SSH in and run <code class="text-neutral-300 bg-white/5 px-1.5 py-0.5 rounded text-sm">mysqldump</code>. When we deploy code, we SSH in and run <code class="text-neutral-300 bg-white/5 px-1.5 py-0.5 rounded text-sm">git pull</code>. Every operation goes through the same encrypted tunnel.
                </p>
            </div>
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">~/.ssh/authorized_keys</span>
                </div>
                <div class="p-5 font-mono text-xs space-y-3">
                    <div class="text-neutral-600"># Your personal key</div>
                    <div class="text-neutral-400 break-all">ssh-ed25519 AAAAC3Nza...your-key user@laptop</div>
                    <div class="text-neutral-600 mt-4"># WPGrip's key (this is all we need)</div>
                    <div class="text-emerald-400 break-all">ssh-rsa AAAAB3NzaC1y...wpgrip-key wpgrip</div>
                    <div class="mt-4 pt-4 border-t border-white/5 text-neutral-600">
                        # No WordPress plugin. No database token.
                        <br># No API endpoint. Just this line.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- COMPARISON TABLE --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">Comparison</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight">Plugin-based vs SSH-native</h2>
        </div>
        <div class="max-w-3xl mx-auto">
            <div class="rounded-xl border border-white/10 overflow-hidden">
                <div class="grid grid-cols-3 bg-neutral-800/50 border-b border-white/5">
                    <div class="p-4 text-xs font-mono text-neutral-400"></div>
                    <div class="p-4 text-xs font-mono text-neutral-400 text-center">Plugin-based tools</div>
                    <div class="p-4 text-xs font-mono text-emerald-400 text-center">WPGrip (SSH)</div>
                </div>
                @php
                $rows = [
                    ['label' => 'Code on your WordPress', 'plugin' => 'PHP plugin on every site', 'ssh' => 'Nothing installed'],
                    ['label' => 'Performance impact', 'plugin' => 'Loads on every page request', 'ssh' => 'Zero — only connects when needed'],
                    ['label' => 'Attack surface', 'plugin' => 'REST/AJAX endpoints exposed', 'ssh' => 'No endpoints exposed'],
                    ['label' => 'Works during fatal errors', 'plugin' => 'No — plugin can\'t execute', 'ssh' => 'Yes — SSH is independent of WP'],
                    ['label' => 'Credential storage', 'plugin' => 'Tokens in WP database', 'ssh' => 'SSH key on server filesystem'],
                    ['label' => 'Plugin conflicts', 'plugin' => 'Possible with security/cache plugins', 'ssh' => 'Impossible — no plugin to conflict'],
                    ['label' => 'Update maintenance', 'plugin' => 'Must update connector plugin', 'ssh' => 'Nothing to update on your server'],
                    ['label' => 'Revoking access', 'plugin' => 'Deactivate plugin on each site', 'ssh' => 'Remove one line from authorized_keys'],
                ];
                @endphp
                @foreach($rows as $i => $row)
                <div class="grid grid-cols-3 {{ $i % 2 === 0 ? 'bg-white/[0.01]' : 'bg-white/[0.03]' }} border-b border-white/5 last:border-0">
                    <div class="p-4 text-sm text-neutral-300 font-medium">{{ $row['label'] }}</div>
                    <div class="p-4 text-sm text-neutral-500 text-center">{{ $row['plugin'] }}</div>
                    <div class="p-4 text-sm text-emerald-400 text-center">{{ $row['ssh'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- SECURITY DEEP DIVE --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-3xl mx-auto">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">Security</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight mb-8">
                SSH is battle-tested infrastructure security
            </h2>
            <div class="space-y-6 text-base text-neutral-400 font-light leading-relaxed">
                <p>
                    SSH (Secure Shell) has been the standard for secure remote server access since 1995. Every hosting provider supports it. Every server ships with it. The protocol is reviewed, audited, and hardened by the global security community.
                </p>
                <p>
                    When WPGrip connects to your server, it uses the same encrypted tunnel that you use when you SSH in from your terminal. The connection is authenticated with a cryptographic key pair — no passwords transmitted, no tokens stored in databases.
                </p>
                <p>
                    Revoking access is trivial: delete one line from your <code class="text-neutral-300 bg-white/5 px-1.5 py-0.5 rounded text-sm">~/.ssh/authorized_keys</code> file. No plugin to deactivate across dozens of sites. No dashboard settings to change. One line, one file, done.
                </p>
                <p>
                    Compare that to plugin-based tools where access tokens live in your WordPress database, API endpoints stay registered in your WordPress installation, and revoking access means logging into every site's admin panel individually.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- SPEED --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="text-xs text-neutral-500 font-mono">benchmark</span>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <div class="flex justify-between text-xs font-mono mb-2">
                            <span class="text-neutral-400">Plugin-based: list plugins</span>
                            <span class="text-neutral-500">~3.2s</span>
                        </div>
                        <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                            <div class="h-full rounded-full bg-neutral-600" style="width: 100%"></div>
                        </div>
                        <div class="text-[10px] text-neutral-600 mt-1">HTTP request → WordPress bootstrap → Plugin hooks → REST response → Parse</div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-mono mb-2">
                            <span class="text-emerald-400">WPGrip SSH: list plugins</span>
                            <span class="text-emerald-400">~0.8s</span>
                        </div>
                        <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500" style="width: 25%"></div>
                        </div>
                        <div class="text-[10px] text-neutral-600 mt-1">SSH connect → WP-CLI exec → Done</div>
                    </div>
                    <div class="pt-4 border-t border-white/5">
                        <div class="flex justify-between text-xs font-mono mb-2">
                            <span class="text-neutral-400">Plugin-based: database backup</span>
                            <span class="text-neutral-500">~45s</span>
                        </div>
                        <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                            <div class="h-full rounded-full bg-neutral-600" style="width: 100%"></div>
                        </div>
                        <div class="text-[10px] text-neutral-600 mt-1">WordPress PHP → Chunk DB → HTTP upload → Verify</div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-mono mb-2">
                            <span class="text-emerald-400">WPGrip SSH: database backup</span>
                            <span class="text-emerald-400">~12s</span>
                        </div>
                        <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500" style="width: 27%"></div>
                        </div>
                        <div class="text-[10px] text-neutral-600 mt-1">SSH connect → Native mysqldump → Encrypt → Transfer</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">Speed</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    WP-CLI over SSH is faster than HTTP APIs
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Plugin-based tools send HTTP requests to your WordPress site, which triggers a full WordPress bootstrap (loading all plugins, themes, and hooks) before executing the management command. Then the response travels back over HTTP.
                </p>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    WPGrip runs WP-CLI directly on the server. WP-CLI is purpose-built for CLI execution — it skips unnecessary WordPress bootstrapping and outputs results directly. No HTTP overhead. No unnecessary plugin loading.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f0f0f 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-semibold text-white tracking-tight">Common questions</h2>
            </div>
            <div class="space-y-4">
                @php
                $faqs = [
                    ['q' => 'Do I need root access?', 'a' => 'No. WPGrip needs SSH access as the user that owns the WordPress files. This is the same user you use for SFTP or when you SSH in to manage the site manually.'],
                    ['q' => 'What if my hosting doesn\'t provide SSH access?', 'a' => 'WPGrip requires SSH access. Most professional hosting providers (VPS, dedicated, managed WordPress) include SSH. If your hosting doesn\'t offer SSH, it may be time to consider a provider that does.'],
                    ['q' => 'Is WP-CLI already installed on my server?', 'a' => 'Most managed WordPress hosts include WP-CLI. If it\'s not installed, you (or your hosting provider) can install it in under a minute. WPGrip checks for WP-CLI during the first connection.'],
                    ['q' => 'Can I revoke WPGrip\'s access?', 'a' => 'Remove the WPGrip public key from ~/.ssh/authorized_keys on your server. One line, one file. Access is revoked immediately.'],
                    ['q' => 'Is SSH less convenient than a plugin?', 'a' => 'The initial setup takes 2 minutes: copy an SSH key, paste it into your server\'s authorized_keys file. After that, everything works the same way — except your WordPress stays clean and fast.'],
                ];
                @endphp
                @foreach($faqs as $faq)
                <div class="rounded-xl border border-white/5 bg-white/[0.02] p-6">
                    <h3 class="text-base font-medium text-white mb-2">{{ $faq['q'] }}</h3>
                    <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $faq['a'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(16,185,129,0.12) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-28 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight text-balance">
            Manage WordPress the way servers were meant to be managed
        </h2>
        <p class="mt-3 text-lg text-neutral-400 font-light">Free trial. No credit card. No plugins to install.</p>
        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-emerald-500 bg-emerald-600 text-emerald-50 hover:bg-emerald-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-emerald-900/40">
                Start your free trial
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                View pricing
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
