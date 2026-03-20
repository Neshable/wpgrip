<?php

namespace App\Ai\Middleware;

use App\Models\AiTokenUsage;
use Closure;
use Filament\Facades\Filament;
use Laravel\Ai\Prompts\AgentPrompt;

class TrackTokenUsage
{
    /**
     * Handle the incoming prompt.
     */
    public function handle(AgentPrompt $prompt, Closure $next)
    {
        $tenant = $this->resolveTenant();

        // Check monthly token limit before proceeding
        if ($tenant && AiTokenUsage::hasExceededLimit($tenant->uuid)) {
            throw new \RuntimeException(
                'Monthly AI token limit reached (5M tokens). Resets on the 1st of next month.'
            );
        }

        return $next($prompt)->then(function ($response) use ($prompt, $tenant) {
            if (! $tenant) {
                return;
            }

            $site = $prompt->agent->site ?? null;
            $usage = $response->usage ?? null;

            $inputTokens = $usage->promptTokens ?? 0;
            $outputTokens = $usage->completionTokens ?? 0;

            AiTokenUsage::create([
                'tenant_id'     => $tenant->uuid,
                'user_id'       => auth()->id(),
                'site_id'       => $site?->id,
                'input_tokens'  => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens'  => $inputTokens + $outputTokens,
                'model'         => $response->meta->model ?? 'unknown',
            ]);
        });
    }

    /**
     * Resolve the current tenant from Filament or from the authenticated user.
     */
    private function resolveTenant(): ?object
    {
        // Try Filament context first
        try {
            $tenant = Filament::getTenant();
            if ($tenant) {
                return $tenant;
            }
        } catch (\Throwable $e) {
            // Not in Filament panel context
        }

        // Fallback: get tenant from authenticated user
        $user = auth()->user();
        if ($user && method_exists($user, 'tenants')) {
            return $user->tenants()->first();
        }

        return null;
    }
}
