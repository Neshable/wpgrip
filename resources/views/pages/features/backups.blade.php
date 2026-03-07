<x-layouts.app>
<x-slot name="title">WordPress Database Backups — Encrypted, Automated, One-Click Restore</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(37,99,235,0.35) 0%, rgba(37,99,235,0.10) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
            Backups
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            Encrypted DB backups.<br>One-click restore.
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            WPGrip backs up your WordPress databases over SSH using mysqldump. Encrypted, compressed, and stored in the cloud. When you need to restore, it takes one click. File backups are your hosting provider's job — we focus on the data that matters most.
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

{{-- BACKUP HISTORY MOCK --}}
<section class="py-24 bg-[#0a0a0a]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-amber-900 bg-amber-950 text-amber-300 text-xs font-mono mb-6">Backup History</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Every database backup. Dated, sized, restorable.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Your dashboard shows every database backup for every site — when it ran, how large the dump is, and whether it completed. Pick any backup and restore your database with a single click.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Full database backup history with dates and sizes',
                        'Status indicator for each backup — completed or failed',
                        'One-click restore from any point in time',
                        'Download backups directly to your machine',
                        'Retention settings to control how many backups to keep',
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
                    <span class="ml-3 text-xs text-neutral-500 font-mono">backups · client-site.com</span>
                </div>
                <div class="p-6 space-y-2">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs text-neutral-500 font-mono uppercase tracking-wide">Recent Backups</span>
                        <span class="text-xs text-emerald-400 font-mono">Auto · Daily at 03:00 UTC</span>
                    </div>
                    @foreach([
                        ['date' => 'May 27, 2025 — 03:00', 'size' => '24.8 MB', 'status' => 'Completed', 'c' => 'emerald'],
                        ['date' => 'May 26, 2025 — 03:00', 'size' => '24.6 MB', 'status' => 'Completed', 'c' => 'emerald'],
                        ['date' => 'May 25, 2025 — 03:00', 'size' => '24.6 MB', 'status' => 'Completed', 'c' => 'emerald'],
                        ['date' => 'May 24, 2025 — 03:00', 'size' => '24.3 MB', 'status' => 'Completed', 'c' => 'emerald'],
                        ['date' => 'May 23, 2025 — 03:00', 'size' => '—',       'status' => 'Failed',    'c' => 'rose'],
                        ['date' => 'May 22, 2025 — 03:00', 'size' => '24.1 MB', 'status' => 'Completed', 'c' => 'emerald'],
                    ] as $backup)
                    @php
                    $dot = ['emerald'=>'bg-emerald-500','rose'=>'bg-rose-500'][$backup['c']];
                    $txt = ['emerald'=>'text-emerald-400','rose'=>'text-rose-400'][$backup['c']];
                    @endphp
                    <div class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.02] px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
                            <div class="flex flex-col">
                                <span class="text-sm text-neutral-200 font-mono">{{ $backup['date'] }}</span>
                                <span class="text-xs text-neutral-500">{{ $backup['size'] }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs {{ $txt }} font-mono">{{ $backup['status'] }}</span>
                            @if($backup['c'] === 'emerald')
                            <button class="inline-flex items-center px-2.5 py-1 rounded border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 text-xs font-mono transition-colors">Restore</button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- HOW BACKUPS WORK --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-neutral-700 bg-neutral-900 text-neutral-300 text-xs font-mono mb-6">How It Works</div>
            <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                SSH in. Dump the database. Encrypt. Store.
            </h2>
            <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                WPGrip connects to your server over SSH, runs mysqldump to export your WordPress database, encrypts the dump, and uploads it to secure cloud storage. No WordPress plugin touches the process. No PHP memory limits to worry about.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
            $steps = [
                ['step' => '01', 'title' => 'SSH connection', 'desc' => 'WPGrip connects to your server using the SSH key you\'ve already authorized. No new credentials needed.'],
                ['step' => '02', 'title' => 'Database export', 'desc' => 'We run mysqldump directly on the server. This bypasses PHP entirely — no memory limits, no timeout issues, no plugin overhead.'],
                ['step' => '03', 'title' => 'Encrypt & compress', 'desc' => 'The SQL dump is compressed and encrypted before it leaves your server. Data is protected in transit and at rest.'],
                ['step' => '04', 'title' => 'Cloud storage', 'desc' => 'The encrypted backup is stored in secure offsite cloud storage. It stays there until you restore it or your retention policy removes it.'],
            ];
            @endphp
            @foreach($steps as $s)
            <div class="rounded-xl border border-white/5 bg-white/[0.02] p-7 flex flex-col gap-4 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-300">
                <div class="inline-flex w-fit items-center px-2.5 py-1 rounded-md border border-amber-900 bg-amber-950 text-amber-300 text-xs font-mono">Step {{ $s['step'] }}</div>
                <h3 class="text-lg font-medium text-white leading-snug">{{ $s['title'] }}</h3>
                <p class="text-sm text-neutral-400 font-light leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SCHEDULING --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">backup schedule</span>
                </div>
                <div class="p-6 space-y-4">
                    @foreach([
                        ['site' => 'client-site.com',      'freq' => 'Daily',   'time' => '03:00 UTC', 'next' => 'In 8 hours',  'c' => 'emerald'],
                        ['site' => 'agency-portfolio.net', 'freq' => 'Daily',   'time' => '04:00 UTC', 'next' => 'In 9 hours',  'c' => 'emerald'],
                        ['site' => 'shop.example.com',     'freq' => 'Twice daily', 'time' => '03:00 / 15:00 UTC', 'next' => 'In 2 hours', 'c' => 'blue'],
                        ['site' => 'staging.project.io',   'freq' => 'Weekly',  'time' => 'Sun 02:00 UTC', 'next' => 'In 3 days', 'c' => 'neutral'],
                    ] as $sched)
                    @php
                    $freqColor = ['emerald'=>'border-emerald-900 bg-emerald-950 text-emerald-300','blue'=>'border-blue-900 bg-blue-950 text-blue-300','neutral'=>'border-neutral-700 bg-neutral-800 text-neutral-300'][$sched['c']];
                    @endphp
                    <div class="rounded-lg border border-white/5 bg-white/[0.02] px-4 py-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-200 font-mono">{{ $sched['site'] }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded border text-xs font-mono {{ $freqColor }}">{{ $sched['freq'] }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-neutral-500 font-mono">{{ $sched['time'] }}</span>
                            <span class="text-xs text-neutral-500 font-mono">Next: {{ $sched['next'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-blue-900 bg-blue-950 text-blue-300 text-xs font-mono mb-6">Scheduling</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Set the schedule. Forget about it.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Choose when backups run for each site — daily, twice daily, or weekly. Pick the time that works for your server's traffic patterns. WPGrip handles the rest.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Per-site scheduling — different sites, different schedules',
                        'Daily, twice daily, or weekly frequency',
                        'Choose the exact time (UTC) for each backup',
                        'Backups run in the background over SSH',
                        'Failed backup alerts so you know when something went wrong',
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

{{-- ENCRYPTION --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">Encryption</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Your data is encrypted at every step
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Database dumps contain sensitive data — user emails, passwords, orders, personal information. WPGrip encrypts every backup before it leaves your server. The encrypted file is transferred over SSH and stored in cloud storage where it remains encrypted at rest.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Encrypted before leaving your server',
                        'Transferred over SSH — encrypted in transit',
                        'Stored encrypted at rest in cloud storage',
                        'No unencrypted database dumps sitting on disk',
                        'Your database credentials never leave your server',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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
                    <span class="ml-3 text-xs text-neutral-500 font-mono">backup process</span>
                </div>
                <div class="p-6 font-mono text-xs space-y-2">
                    <div class="text-neutral-500">→ Connecting to client-site.com via SSH...</div>
                    <div class="text-emerald-400">✓ Connected as wpgrip@192.168.1.10</div>
                    <div class="text-neutral-500">→ Running mysqldump on wp_clientsite...</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;Tables: 42 · Rows: 128,491</div>
                    <div class="text-emerald-400">✓ Database exported — 31.2 MB</div>
                    <div class="text-neutral-500">→ Compressing...</div>
                    <div class="text-emerald-400">✓ Compressed — 24.8 MB</div>
                    <div class="text-neutral-500">→ Encrypting with AES-256...</div>
                    <div class="text-emerald-400">✓ Encrypted</div>
                    <div class="text-neutral-500">→ Uploading to cloud storage...</div>
                    <div class="text-emerald-400">✓ Stored — client-site_2025-05-27_030000.sql.gz.enc</div>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <div class="text-emerald-400">✓ Backup complete in 18s</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- RESTORE PROCESS --}}
<section class="py-24 border-t border-white/5" style="background: linear-gradient(180deg, #0a0a0a 0%, #0f1117 100%)">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    <span class="ml-3 text-xs text-neutral-500 font-mono">restore</span>
                </div>
                <div class="p-6 font-mono text-xs space-y-2">
                    <div class="text-neutral-500">→ Restore requested: client-site_2025-05-26_030000.sql.gz.enc</div>
                    <div class="text-neutral-500">→ Downloading from cloud storage...</div>
                    <div class="text-emerald-400">✓ Downloaded — 24.6 MB</div>
                    <div class="text-neutral-500">→ Decrypting...</div>
                    <div class="text-emerald-400">✓ Decrypted</div>
                    <div class="text-neutral-500">→ Decompressing...</div>
                    <div class="text-emerald-400">✓ Decompressed — 30.9 MB</div>
                    <div class="text-neutral-500">→ Connecting to client-site.com via SSH...</div>
                    <div class="text-emerald-400">✓ Connected</div>
                    <div class="text-neutral-500">→ Importing to wp_clientsite...</div>
                    <div class="text-neutral-400">&nbsp;&nbsp;Tables: 42 · Rows: 127,843</div>
                    <div class="text-emerald-400">✓ Database restored</div>
                    <div class="text-neutral-500">→ Flushing WP cache...</div>
                    <div class="text-emerald-400">✓ Cache cleared</div>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <div class="text-emerald-400">✓ Restore complete in 34s</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-violet-900 bg-violet-950 text-violet-300 text-xs font-mono mb-6">Restore</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    One click to roll back
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    Pick any backup from your history and hit Restore. WPGrip downloads the encrypted backup, decrypts it, connects to your server over SSH, and imports the database. The whole process takes seconds to minutes depending on database size.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Restore any backup from your history',
                        'One-click trigger from the dashboard',
                        'Full restore log so you see every step',
                        'Automatic cache flush after restore',
                        'No SSH access required from your end — WPGrip handles it',
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

{{-- NO PLUGIN ADVANTAGE --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono mb-6">No Plugin Needed</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Plugin-based backups have real limits
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    WordPress backup plugins run inside PHP. They're limited by your server's memory, execution time, and upload limits. Large databases time out. Shared hosting kills the process. And every backup plugin adds weight to your WordPress install.
                </p>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    WPGrip runs mysqldump directly on the server via SSH. It's the same tool your hosting provider uses for their own backups. No PHP involved. No WordPress bootstrap. No memory limits.
                </p>
            </div>
            <div class="space-y-4">
                <div class="rounded-xl border border-white/5 bg-white/[0.02] p-6">
                    <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono mb-4">Plugin-based backup</div>
                    <ul class="space-y-2">
                        @foreach([
                            'Runs inside PHP with memory and time limits',
                            'Adds a plugin to your WordPress install',
                            'Stores temp files on your web server',
                            'Can fail silently on large databases',
                            'Competes with page requests for resources',
                        ] as $f)
                        <li class="flex items-center gap-2 text-sm text-neutral-400">
                            <svg class="h-4 w-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-xl border border-emerald-500/20 bg-emerald-950/10 p-6">
                    <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-4">WPGrip backup</div>
                    <ul class="space-y-2">
                        @foreach([
                            'Runs mysqldump over SSH — no PHP involved',
                            'Zero plugins installed on your site',
                            'No temp files on your web server',
                            'Handles databases of any size',
                            'Zero impact on your site\'s performance',
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
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(30,64,175,0.15) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-28 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight text-balance">
            Your safety net. Set it once.
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
