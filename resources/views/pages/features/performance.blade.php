<x-layouts.app>
<x-slot name="title">WordPress Performance Testing — Lighthouse Scores Tracked Over Time</x-slot>

{{-- HERO --}}
<section class="relative w-full overflow-hidden bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse 90% 65% at 50% -10%, rgba(16,185,129,0.25) 0%, rgba(16,185,129,0.08) 40%, transparent 70%);"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to bottom, transparent, #0a0a0a);"></div>

    <div class="relative max-w-screen-xl mx-auto px-6 flex flex-col items-center text-center pt-40 pb-20">
        <div class="inline-flex items-center gap-2 mb-6 px-3 py-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 text-xs font-medium tracking-wide uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
            Performance
        </div>
        <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white text-balance leading-tight max-w-3xl">
            Track PageSpeed scores.<br><span class="text-emerald-400">Catch regressions early.</span>
        </h1>
        <p class="mt-6 text-lg text-neutral-400 font-light max-w-xl leading-relaxed">
            WPGrip runs Google Lighthouse tests on a schedule and stores the results. You get mobile and desktop scores, historical trends, and alerts when a score drops — so you can trace it back to the exact plugin update or deploy that caused it.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-emerald-500 bg-emerald-600 text-emerald-50 hover:bg-emerald-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-emerald-900/40">
                Start testing for free
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                See pricing
            </a>
        </div>
    </div>
</section>

{{-- SCORE DASHBOARD MOCK --}}
<section class="py-24 bg-[#0a0a0a]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-emerald-900 bg-emerald-950 text-emerald-300 text-xs font-mono mb-6">Lighthouse Scores</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    Mobile and desktop. Every day.
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    WPGrip runs Lighthouse using real Chromium on a daily schedule. You get Performance, Accessibility, Best Practices, and SEO scores for both mobile and desktop — with full history so you can spot trends.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Google Lighthouse via real Chromium',
                        'Mobile and desktop scores',
                        'Performance, Accessibility, Best Practices, SEO',
                        'Historical score tracking with trend graphs',
                        'Daily automated tests',
                        'On-demand manual tests',
                    ] as $f)
                    <li class="flex items-start gap-3 text-sm text-neutral-300">
                        <svg class="h-5 w-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-xl border border-white/10 bg-neutral-900 p-6">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-xs text-neutral-500 font-mono uppercase tracking-wide">client-shop.com · Latest Test</span>
                    <span class="text-xs text-neutral-600 font-mono">Mobile</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['label' => 'Performance', 'score' => 87, 'c' => 'emerald'],
                        ['label' => 'Accessibility', 'score' => 94, 'c' => 'emerald'],
                        ['label' => 'Best Practices', 'score' => 100, 'c' => 'emerald'],
                        ['label' => 'SEO', 'score' => 91, 'c' => 'emerald'],
                    ] as $metric)
                    <div class="rounded-lg border border-white/5 bg-white/[0.02] p-4 text-center">
                        <div class="text-3xl font-semibold text-{{ $metric['c'] }}-400">{{ $metric['score'] }}</div>
                        <div class="text-xs text-neutral-500 mt-1">{{ $metric['label'] }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6 pt-4 border-t border-white/5">
                    <div class="text-xs text-neutral-600 font-mono mb-3">Performance trend (30 days)</div>
                    <div class="flex items-end gap-1 h-12">
                        @foreach([78,80,82,79,81,84,83,86,85,87,84,82,85,87,88,86,84,82,78,75,72,74,78,80,83,85,86,87,87,87] as $v)
                        <div class="flex-1 rounded-sm {{ $v >= 85 ? 'bg-emerald-500' : ($v >= 75 ? 'bg-amber-500' : 'bg-rose-500') }}" style="height: {{ ($v - 60) * 100 / 40 }}%"></div>
                        @endforeach
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-[10px] text-neutral-700 font-mono">30d ago</span>
                        <span class="text-[10px] text-neutral-700 font-mono">Today</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- REGRESSION DETECTION --}}
<section class="py-24 border-t border-white/5 bg-neutral-950">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="rounded-xl border border-white/10 bg-neutral-900 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-neutral-800/50">
                    <span class="text-xs text-neutral-500 font-mono">performance alert</span>
                </div>
                <div class="p-5 space-y-4 font-mono text-xs">
                    <div class="flex items-start gap-3">
                        <span class="text-rose-400 mt-0.5">●</span>
                        <div>
                            <div class="text-neutral-300">client-shop.com mobile score dropped 87 → 72</div>
                            <div class="text-neutral-600">Today at 01:15 · Regression detected</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-blue-400 mt-0.5">●</span>
                        <div>
                            <div class="text-neutral-300">Possible cause: Yoast SEO updated 21.8 → 21.9 yesterday</div>
                            <div class="text-neutral-600">Added 48kB undeferred JavaScript</div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="inline-flex items-center px-2.5 py-1 rounded-md border border-rose-900 bg-rose-950 text-rose-300 text-xs font-mono mb-6">Regression Detection</div>
                <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight leading-tight">
                    A score dropped. What changed?
                </h2>
                <p class="mt-4 text-base text-neutral-400 font-light leading-relaxed">
                    When a Lighthouse score drops, WPGrip helps you trace it back. Compare the score timeline against plugin updates, theme changes, and deployments. The AI assistant can even investigate the specific files causing the regression.
                </p>
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Automatic regression detection',
                        'Timeline correlated with updates and deploys',
                        'AI assistant can investigate root causes',
                        'Per-metric history (not just overall score)',
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

{{-- CTA --}}
<section class="relative overflow-hidden border-t border-white/10 bg-neutral-950">
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(60% 50% at 50% 100%, rgba(16,185,129,0.12) 0%, transparent 80%);"></div>
    <div class="relative max-w-2xl mx-auto px-6 py-28 text-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-white tracking-tight text-balance">
            Know your scores before Google does
        </h2>
        <p class="mt-3 text-lg text-neutral-400 font-light">Free trial. No credit card. No plugins to install.</p>
        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-3">
            <a href="/register" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-emerald-500 bg-emerald-600 text-emerald-50 hover:bg-emerald-500 font-medium text-sm transition-all duration-200 shadow-lg shadow-emerald-900/40">
                Start testing for free
            </a>
            <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-white/10 bg-white/5 text-neutral-300 hover:bg-white/10 hover:text-white text-sm transition-all duration-200">
                View pricing
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
