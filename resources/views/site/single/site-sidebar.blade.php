@php
    $tenant = Filament\Facades\Filament::getTenant();
    $site   = $this->getRecord();
    $server = $site->server;
    $recordId = $site->id;
    $tenantUuid = $tenant->uuid;
    $canAI  = \App\Services\Plans\SubscriptionLimitChecker::canUseAiSilent();
    $canGit = \App\Services\Plans\SubscriptionLimitChecker::canUseGitSilent();
    $isStaging = (bool) $site->is_staging;

    // Helper to build route + check active
    $r = fn(string $name) => route("filament.dashboard.resources.sites.{$name}", ['record' => $recordId, 'tenant' => $tenantUuid]);
    // Use the Livewire component's page class to determine active state
    $currentPageClass = get_class($this);
    $pageRouteMap = [
        'view' => \App\Filament\Dashboard\Resources\SiteResource\Pages\ViewSite::class,
        'ai-assistant' => \App\Filament\Dashboard\Resources\SiteResource\Pages\AiAssistant::class,
        'agent-mode' => \App\Filament\Dashboard\Resources\SiteResource\Pages\AgentMode::class,
        'files' => \App\Filament\Dashboard\Resources\SiteResource\Pages\FileExplorer::class,
        'monitoring' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Monitoring::class,
        'backups' => \App\Filament\Dashboard\Resources\SiteResource\Pages\ShowBackups::class,
        'plugins' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Plugins::class,
        'themes' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Themes::class,
        'core' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Core::class,
        'performance' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Performance::class,
        'repositories' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Repositories::class,
        'staging' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Staging::class,
        'tools' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Tools::class,
        'security' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Security::class,
        'errors' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Errors::class,
        'access' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Access::class,
        'insights' => \App\Filament\Dashboard\Resources\SiteResource\Pages\Insights::class,

    ];
    $isActive = fn(string $name) => isset($pageRouteMap[$name]) && $currentPageClass === $pageRouteMap[$name];

    // Build the navigation structure: groups with items
    $groups = [
        [
            'label' => null, // no group label for top items
            'items' => [
                ['label' => 'Dashboard', 'route' => 'view'],
            ],
        ],
        [
            'label' => 'Management',
            'items' => array_filter([
                $canAI ? ['label' => 'AI Assistant', 'route' => 'ai-assistant'] : null,
                $canAI ? ['label' => 'Agent Mode', 'route' => 'agent-mode'] : null,
                ['label' => 'File Manager', 'route' => 'files'],
                ['label' => 'Monitoring', 'route' => 'monitoring'],
                ['label' => 'Backups', 'route' => 'backups'],
            ]),
        ],
        [
            'label' => 'WordPress',
            'items' => array_filter([
                ['label' => 'Plugins', 'route' => 'plugins'],
                ['label' => 'Themes', 'route' => 'themes'],
                ['label' => 'Core Updates', 'route' => 'core'],
                !$isStaging ? ['label' => 'Performance', 'route' => 'performance'] : null,
            ]),
        ],
        [
            'label' => 'Development',
            'items' => array_filter([
                $canGit ? ['label' => 'Repositories', 'route' => 'repositories'] : null,
                !$isStaging ? ['label' => 'Staging', 'route' => 'staging'] : null,
                ['label' => 'Dev Tools', 'route' => 'tools'],
            ]),
        ],
        !$isStaging ? [
            'label' => 'Security',
            'items' => [
                ['label' => 'Security', 'route' => 'security'],
                ['label' => 'Errors', 'route' => 'errors'],
            ],
        ] : null,
    ];
@endphp

<aside class="site-sub-sidebar">
    {{-- Site info card --}}
    <div class="site-sub-sidebar-header">
        <div class="flex items-center gap-3 px-4 py-3">
            <img
                src="https://s2.googleusercontent.com/s2/favicons?domain={{ $site->url }}&sz=32"
                alt=""
                class="w-6 h-6 rounded flex-shrink-0"
                loading="lazy"
            />
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $site->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $server?->ip ?? '' }}</p>
            </div>
        </div>
    </div>

    {{-- Back link --}}
    <div class="px-4 py-2">
        <a
            href="{{ route('filament.dashboard.resources.sites.index', ['tenant' => $tenantUuid]) }}"
            wire:navigate
            class="inline-flex items-center gap-1.5 text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 transition-colors"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Back
        </a>
    </div>

    {{-- Navigation groups --}}
    <nav class="site-sub-sidebar-nav">
        @foreach(array_filter($groups) as $group)
            @if(count($group['items']) > 0)
                <div class="site-sub-sidebar-group">
                    @if($group['label'])
                        <p class="site-sub-sidebar-group-label">{{ $group['label'] }}</p>
                    @endif
                    <ul>
                        @foreach($group['items'] as $item)
                            @if($item)
                                <li>
                                    <a
                                        href="{{ $r($item['route']) }}"
                                        wire:navigate
                                        @class([
                                            'site-sub-sidebar-item',
                                            'site-sub-sidebar-item-active' => $isActive($item['route']),
                                        ])
                                    >
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </nav>
</aside>


