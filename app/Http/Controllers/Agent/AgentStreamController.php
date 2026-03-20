<?php

namespace App\Http\Controllers\Agent;

use App\Ai\Agents\SiteAgent;
use App\Models\AiTokenUsage;
use App\Models\Site;
use App\Models\Tenant;
use App\Services\Plans\SubscriptionLimitChecker;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Streaming\Events\ReasoningDelta;
use Laravel\Ai\Streaming\Events\ReasoningEnd;
use Laravel\Ai\Streaming\Events\ReasoningStart;
use Laravel\Ai\Streaming\Events\StreamEnd;
use Laravel\Ai\Streaming\Events\StreamStart;
use Laravel\Ai\Streaming\Events\TextDelta;
use Laravel\Ai\Streaming\Events\TextEnd;
use Laravel\Ai\Streaming\Events\TextStart;
use Laravel\Ai\Streaming\Events\ToolCall;
use Laravel\Ai\Streaming\Events\ToolResult;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AgentStreamController
{
    public function stream(Request $request, string $tenant, Site $site): StreamedResponse
    {
        $request->validate([
            'message' => 'required|string|max:10000',
            'conversation_id' => 'nullable|string|max:36',
        ]);

        // Auth & tenant checks
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        // Resolve tenant from route parameter (UUID)
        $tenantModel = Tenant::where('uuid', $tenant)->first();
        if (! $tenantModel || $site->tenant_id !== $tenantModel->id) {
            abort(403);
        }

        // Set Filament tenant context if possible
        try {
            Filament::setTenant($tenantModel);
        } catch (\Throwable $e) {
            // Not in Filament panel context, that's fine
        }

        if (AiTokenUsage::hasExceededLimit($tenantModel->uuid)) {
            abort(429, 'Monthly AI token limit reached.');
        }

        $message = $request->input('message');
        $conversationId = $request->input('conversation_id');

        return new StreamedResponse(function () use ($site, $user, $message, $conversationId, $tenantModel) {
            // Disable output buffering for real streaming
            if (ob_get_level()) {
                ob_end_clean();
            }

            try {
                $agent = new SiteAgent($site);

                if ($conversationId) {
                    $agent->continue($conversationId, as: $user);
                } else {
                    $agent->forUser($user);
                }

                $stream = $agent->stream($message);
                $newConversationId = null;

                foreach ($stream as $event) {
                    $data = match (true) {
                        $event instanceof StreamStart => [
                            'type' => 'stream_start',
                            'provider' => $event->provider,
                            'model' => $event->model,
                        ],
                        $event instanceof ReasoningStart => [
                            'type' => 'reasoning_start',
                        ],
                        $event instanceof ReasoningDelta => [
                            'type' => 'reasoning_delta',
                            'delta' => $event->delta,
                        ],
                        $event instanceof ReasoningEnd => [
                            'type' => 'reasoning_end',
                        ],
                        $event instanceof TextStart => [
                            'type' => 'text_start',
                        ],
                        $event instanceof TextDelta => [
                            'type' => 'text_delta',
                            'delta' => $event->delta,
                        ],
                        $event instanceof TextEnd => [
                            'type' => 'text_end',
                        ],
                        $event instanceof ToolCall => [
                            'type' => 'tool_call',
                            'tool_name' => $event->toolCall->name,
                            'arguments' => $event->toolCall->arguments,
                        ],
                        $event instanceof ToolResult => [
                            'type' => 'tool_result',
                            'tool_name' => $event->toolResult->name,
                            'result' => \Illuminate\Support\Str::limit($event->toolResult->result ?? '', 500),
                            'successful' => $event->successful,
                            'error' => $event->error,
                        ],
                        $event instanceof StreamEnd => [
                            'type' => 'stream_end',
                            'usage' => $event->usage->toArray(),
                        ],
                        default => null,
                    };

                    if ($data !== null) {
                        echo 'data: ' . json_encode($data) . "\n\n";
                        if (function_exists('fastcgi_finish_request')) {
                            // Don't call this — it ends the connection
                        }
                        flush();
                    }
                }

                // After streaming completes, send conversation metadata
                // Note: conversationId is on the agent (set by RememberConversation middleware),
                // not on the StreamableAgentResponse (which passes it to StreamedAgentResponse).
                $conversationId = $agent->currentConversation() ?? $stream->conversationId;
                $tokensUsed = AiTokenUsage::monthlyUsage($tenantModel->uuid);

                Log::info('Agent stream completed', [
                    'conversation_id' => $conversationId,
                    'tokens_used' => $tokensUsed,
                    'text_length' => strlen($stream->text ?? ''),
                ]);

                echo 'data: ' . json_encode([
                    'type' => 'done',
                    'conversation_id' => $conversationId,
                    'tokens_used' => $tokensUsed,
                    'full_text' => $stream->text,
                ]) . "\n\n";
                flush();

            } catch (\Throwable $e) {
                Log::error('Agent stream error: ' . $e->getMessage(), [
                    'site_id' => $site->id,
                    'trace' => $e->getTraceAsString(),
                ]);

                echo 'data: ' . json_encode([
                    'type' => 'error',
                    'message' => $e->getMessage(),
                ]) . "\n\n";
                flush();
            }

            echo "data: [DONE]\n\n";
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // nginx
        ]);
    }
}
