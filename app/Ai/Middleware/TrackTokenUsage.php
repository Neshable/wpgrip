<?php

namespace App\Ai\Middleware;

use App\Models\AiTokenUsage;
use Closure;
use Filament\Facades\Filament;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;

class TrackTokenUsage
{
    /**
     * Handle the incoming prompt.
     */
    public function handle(AgentPrompt $prompt, Closure $next)
    {
        $tenant = Filament::getTenant();

        // Check monthly token limit before proceeding
        if ($tenant && AiTokenUsage::hasExceededLimit($tenant->uuid)) {
            throw new \RuntimeException(
                'Monthly AI token limit reached (5M tokens). Resets on the 1st of next month.'
            );
        }

        return $next($prompt)->then(function (AgentResponse $response) use ($prompt, $tenant) {
            if (! $tenant) {
                return;
            }

            $site = $prompt->agent->site ?? null;

            AiTokenUsage::create([
                'tenant_id'     => $tenant->uuid,
                'user_id'       => auth()->id(),
                'site_id'       => $site?->id,
                'input_tokens'  => $response->usage->promptTokens ?? 0,
                'output_tokens' => $response->usage->completionTokens ?? 0,
                'total_tokens'  => ($response->usage->promptTokens ?? 0) + ($response->usage->completionTokens ?? 0),
                'model'         => $response->meta->model ?? 'unknown',
            ]);
        });
    }
}
