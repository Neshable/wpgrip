<?php

namespace App\Services\Plans;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Site;
use App\Models\Repository;
use App\Models\Backup;

class SubscriptionLimitChecker
{
    /**
     * Product slugs that include Git deployments.
     * Note: "enterprise" plan belongs to the "ultimate" product in DB — no separate enterprise product.
     */
    protected static array $gitPlans = ['pro', 'ultimate'];

    /**
     * Product slugs that include the AI Assistant.
     * Note: "enterprise" plan belongs to the "ultimate" product in DB — no separate enterprise product.
     */
    protected static array $aiPlans = ['pro', 'ultimate'];

    /**
     * Return true if the current tenant's plan includes Git deployments.
     * Shows an upgrade notification when returning false.
     */
    public static function canUseGit(): bool
    {
        if (static::canUseGitSilent()) {
            return true;
        }

        static::showLimitReachedNotification(
            'Git deployments not available',
            'Upgrade to Pro or higher to use Git deployments.'
        );

        return false;
    }

    /**
     * Return true if the current tenant's plan includes Git deployments (no notification).
     * Safe to call from Blade nav-visibility checks.
     */
    public static function canUseGitSilent(): bool
    {
        $user   = Auth::user();
        $tenant = Filament::getTenant();

        foreach (static::$gitPlans as $plan) {
            if ($user->isSubscribed($plan, $tenant)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if the current tenant's plan includes the AI Assistant.
     * Shows an upgrade notification when returning false.
     */
    public static function canUseAi(): bool
    {
        if (static::canUseAiSilent()) {
            return true;
        }

        static::showLimitReachedNotification(
            'AI Assistant not available',
            'Upgrade to Pro or higher to use the AI Assistant.'
        );

        return false;
    }

    /**
     * Return true if the current tenant's plan includes the AI Assistant (no notification).
     * Safe to call from Blade nav-visibility checks.
     */
    public static function canUseAiSilent(): bool
    {
        $user   = Auth::user();
        $tenant = Filament::getTenant();

        foreach (static::$aiPlans as $plan) {
            if ($user->isSubscribed($plan, $tenant)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Site/repo/backup limits keyed by PLAN slug (not product slug).
     * Both Agency (ultimate-monthly/yearly) and Enterprise (enterprise-monthly) share the
     * "ultimate" product but have different site limits — so we key on plan slug.
     */
    protected static array $resourceLimits = [
        'site' => [
            'basic-monthly'      => 5,
            'basic-yearly'       => 5,
            'pro-monthly'        => 20,
            'pro-yearly'         => 20,
            'ultimate-monthly'   => 50,
            'ultimate-yearly'    => 50,
            'enterprise-monthly' => PHP_INT_MAX,
        ],
        'repository' => [
            'basic-monthly'      => 0,
            'basic-yearly'       => 0,
            'pro-monthly'        => PHP_INT_MAX,
            'pro-yearly'         => PHP_INT_MAX,
            'ultimate-monthly'   => PHP_INT_MAX,
            'ultimate-yearly'    => PHP_INT_MAX,
            'enterprise-monthly' => PHP_INT_MAX,
        ],
        'backup' => [
            'basic-monthly'      => 20,
            'basic-yearly'       => 20,
            'pro-monthly'        => 60,
            'pro-yearly'         => 60,
            'ultimate-monthly'   => PHP_INT_MAX,
            'ultimate-yearly'    => PHP_INT_MAX,
            'enterprise-monthly' => PHP_INT_MAX,
        ],
    ];

    protected static array $resourceModels = [
        'site' => Site::class,
        'repository' => Repository::class,
        'backup' => Backup::class,
    ];

    public static function canCreate(string $resourceType): bool
    {
        return static::checkSubscriptionLimit($resourceType);
    }

    protected static function checkSubscriptionLimit(string $resourceType): bool
    {
        $user = Auth::user();
        $tenant = Filament::getTenant();

        $planSlug = static::getActivePlanSlug($tenant);

        if (! $planSlug) {
            static::showLimitReachedNotification('You don\'t have an active subscription!', 'Choose a plan to continue.');
            return false;
        }

        $resourceLimit = static::$resourceLimits[$resourceType][$planSlug] ?? 0;
        $resourceCount = static::getResourceCount($resourceType, $tenant);

        if ($resourceCount >= $resourceLimit) {
            static::showLimitReachedNotification(
                ucfirst($resourceType) . ' limit reached!',
                "You've reached the maximum number of {$resourceType}s for your plan."
            );
            return false;
        }

        return true;
    }

    /**
     * Returns the active plan slug (e.g. "pro-monthly", "enterprise-monthly") for the tenant's
     * current subscription.  Priority: enterprise > ultimate > pro > basic.
     */
    protected static function getActivePlanSlug($tenant): ?string
    {
        // Priority order — enterprise-monthly first so it isn't shadowed by ultimate check
        $priority = [
            'enterprise-monthly',
            'ultimate-yearly', 'ultimate-monthly',
            'pro-yearly',      'pro-monthly',
            'basic-yearly',    'basic-monthly',
        ];

        $activeSlugs = $tenant->subscriptions()
            ->whereIn('status', ['active', 'trialing'])
            ->with('plan')
            ->get()
            ->pluck('plan.slug')
            ->toArray();

        foreach ($priority as $slug) {
            if (in_array($slug, $activeSlugs)) {
                return $slug;
            }
        }

        return null;
    }

    /**
     * Returns the active product slug (highest tier first) for the user+tenant.
     * DB product slugs are: basic, pro, ultimate (enterprise plans also use "ultimate" product).
     *
     * @deprecated Use getActivePlanSlug() for limit checks. This is kept for canUseGit/canUseAi.
     */
    protected static function getSubscribedProductSlug($user, $tenant): ?string
    {
        foreach (['ultimate', 'pro', 'basic'] as $product) {
            if ($user->isSubscribed($product, $tenant)) {
                return $product;
            }
        }
        return null;
    }

    protected static function getResourceCount(string $resourceType, $tenant): int
    {
        $modelClass = static::$resourceModels[$resourceType];
        return $modelClass::where('tenant_id', $tenant->id)->count();
    }

    protected static function showLimitReachedNotification(string $title, string $body): void
    {
        Notification::make()
            ->warning()
            ->title($title)
            ->body($body)
            ->persistent()
            ->send();
    }
}