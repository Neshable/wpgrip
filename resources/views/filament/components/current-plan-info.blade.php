@php
    use App\Models\Site;

    $tenant = Filament\Facades\Filament::getTenant();
    $user   = auth()->user();

    // Detect active plan (highest first)
    $planSlugs = ['enterprise', 'ultimate', 'pro', 'basic'];
    $activePlanSlug = null;
    foreach ($planSlugs as $slug) {
        if ($user->isSubscribed($slug, $tenant)) {
            $activePlanSlug = $slug;
            break;
        }
    }

    // Site limits per plan
    $siteLimits = [
        'basic'      => 5,
        'pro'        => 20,
        'ultimate'   => 50,
        'enterprise' => null,
    ];

    // Display names
    $planLabels = [
        'basic'      => 'Starter',
        'pro'        => 'Pro',
        'ultimate'   => 'Agency',
        'enterprise' => 'Enterprise',
    ];

    // Per-plan colour tokens
    $planColors = [
        'basic'      => ['ring' => 'ring-sky-500/40',    'dot' => 'bg-sky-400',    'bar' => 'bg-sky-500'],
        'pro'        => ['ring' => 'ring-violet-500/40', 'dot' => 'bg-violet-400', 'bar' => 'bg-violet-500'],
        'ultimate'   => ['ring' => 'ring-blue-500/40',   'dot' => 'bg-blue-400',   'bar' => 'bg-blue-500'],
        'enterprise' => ['ring' => 'ring-amber-500/40',  'dot' => 'bg-amber-400',  'bar' => 'bg-amber-500'],
    ];

    $siteCount  = Site::where('tenant_id', $tenant->id)->where('is_staging', false)->count();
    $siteLimit  = $activePlanSlug ? ($siteLimits[$activePlanSlug] ?? null) : null;
    $label      = $activePlanSlug ? ($planLabels[$activePlanSlug] ?? ucfirst($activePlanSlug)) : null;
    $colors     = $activePlanSlug ? ($planColors[$activePlanSlug] ?? $planColors['basic']) : null;
    $pct        = ($siteLimit && $siteLimit > 0) ? min(100, round($siteCount / $siteLimit * 100)) : 0;
    $nearLimit  = $siteLimit && $pct >= 80;
    $isEnterprise = $activePlanSlug === 'enterprise';

    $tenantId   = $tenant->id;
    $sub        = $tenant->subscriptions()->latest()->first();
    $billingUrl = $sub
        ? route('filament.dashboard.resources.subscriptions.view', ['tenant' => $tenantId, 'record' => $sub->id])
        : route('pricing');
@endphp

@if ($activePlanSlug)
<div class="px-3 pb-4 pt-1">
    <div class="rounded-xl overflow-hidden ring-1 {{ $colors['ring'] }} bg-white/[0.03] border border-white/[0.06]">

        {{-- Plan name row --}}
        <div class="flex items-center gap-1.5 px-3 pt-3 pb-2">
            <span class="w-2 h-2 rounded-full {{ $colors['dot'] }} shrink-0"></span>
            <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ $label }}</span>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-light">plan</span>
        </div>

        {{-- Sites usage bar --}}
        <div class="px-3 pb-2.5">
            <div class="flex justify-between items-baseline mb-1">
                <span class="text-[11px] text-gray-500 dark:text-gray-400">Sites used</span>
                <span class="text-[11px] font-mono font-medium {{ $nearLimit ? 'text-amber-400' : 'text-gray-400 dark:text-gray-300' }}">
                    {{ $siteCount }}&thinsp;/&thinsp;{{ $siteLimit ?? '∞' }}
                </span>
            </div>
            <div class="w-full h-1 rounded-full bg-black/20 dark:bg-white/[0.08] overflow-hidden">
                @if ($siteLimit)
                <div class="h-full rounded-full transition-all duration-500 {{ $nearLimit ? 'bg-amber-400' : $colors['bar'] }}"
                     style="width: max(6px, {{ $pct }}%)"></div>
                @else
                <div class="h-full w-full rounded-full {{ $colors['bar'] }} opacity-30"></div>
                @endif
            </div>
            @if ($nearLimit && !$isEnterprise)
            <p class="mt-1 text-[10px] text-amber-400/80 leading-tight">
                {{ $pct >= 100 ? 'Limit reached — upgrade to add more.' : 'Approaching your limit.' }}
            </p>
            @endif
        </div>

        {{-- Bottom action strip --}}
        <div class="flex border-t border-white/[0.06]">
            <a href="{{ $billingUrl }}"
               class="flex-1 flex items-center justify-center gap-1 py-2 text-[11px] text-gray-500 dark:text-gray-400
                      hover:text-gray-800 dark:hover:text-gray-200 hover:bg-white/[0.04] transition-colors">
                <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Billing
            </a>
            @if (!$isEnterprise)
            <a href="{{ route('pricing') }}"
               class="flex-1 flex items-center justify-center gap-1 py-2 text-[11px] font-medium
                      text-primary-400 hover:text-primary-300 hover:bg-primary-500/[0.08]
                      border-l border-white/[0.06] transition-colors">
                <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                Upgrade
            </a>
            @endif
        </div>

    </div>
</div>
@else
{{-- No subscription --}}
<div class="px-3 pb-4 pt-1">
    <div class="rounded-xl p-3 ring-1 ring-amber-500/30 bg-amber-500/5 border border-white/[0.06]">
        <p class="text-xs font-semibold text-amber-400 mb-1">No active plan</p>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-2 leading-snug">Pick a plan to unlock all features.</p>
        <a href="{{ route('pricing') }}"
           class="inline-flex items-center gap-1 text-[11px] font-medium px-2.5 py-1 rounded-md
                  bg-primary-500 text-white hover:bg-primary-400 transition-colors">
            View plans
        </a>
    </div>
</div>
@endif
