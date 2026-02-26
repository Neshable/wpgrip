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
    protected static array $resourceLimits = [
        'site' => [
            'basic' => 5,
            'pro' => 20,
            'ultimate' => 60,
        ],
        'repository' => [
            'basic' => 10,
            'pro' => 40,
            'ultimate' => 100,
        ],
        'backup' => [
            'basic' => 20,
            'pro' => 50,
            'ultimate' => 100,
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
        
        $subscribedPlan = static::getSubscribedPlan($user, $tenant);
        
        if (!$subscribedPlan) {
            static::showLimitReachedNotification('You don\'t have an active subscription!', 'Choose a plan to continue.');
            return false;
        }
        
        $resourceCount = static::getResourceCount($resourceType, $tenant);
        $resourceLimit = static::$resourceLimits[$resourceType][$subscribedPlan];
        
        if ($resourceCount >= $resourceLimit) {
            static::showLimitReachedNotification(
                ucfirst($resourceType) . ' limit reached!',
                "You've reached the maximum number of {$resourceType}s for your {$subscribedPlan} plan."
            );
            return false;
        }
        
        return true;
    }

    protected static function getSubscribedPlan($user, $tenant): ?string
    {
        foreach (array_keys(static::$resourceLimits['site']) as $plan) {
            if ($user->isSubscribed($plan, $tenant)) {
                return $plan;
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