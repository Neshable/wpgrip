@extends('site/single/pagetemplate')

@section('content')

{{-- Styles injected into <head> via Filament render hook in AiAssistant.php --}}

<div
    x-data="aiChat()"
    x-init="init($el)"
    class="flex flex-col"
    style="height: calc(100vh - 220px); min-height: 520px;"
>
    {{-- Header row --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-sparkles" class="w-5 h-5 text-violet-500" />
            <span class="font-semibold text-gray-800 dark:text-white">AI Assistant</span>
            <span class="text-xs text-gray-400 dark:text-gray-500">Powered by Claude</span>
        </div>

        <div class="flex items-center gap-2">
            {{-- Context freshness badge --}}
            <span
                class="inline-flex items-center gap-1 rounded-full border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-800 px-2.5 py-1 text-xs text-gray-500 dark:text-gray-400"
                title="Age of the cached site snapshot (SITE.md)"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Context:
                <span x-text="siteMdAge || '{{ $siteMdAge }}'" class="font-medium"></span>
            </span>

            {{-- Refresh context --}}
            <button
                @click="refreshContext()"
                :disabled="refreshing"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-violet-900/30 hover:border-violet-300 transition disabled:opacity-50"
                title="Regenerate SITE.md from current DB data"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" :class="refreshing ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span x-text="refreshing ? 'Refreshing…' : 'Refresh context'"></span>
            </button>

            {{-- Clear chat --}}
            <button
                @click="clearChat()"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Clear
            </button>
        </div>
    </div>

    {{-- Server-side error banner --}}
    @if ($aiError)
    <div class="mb-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300">
        {{ $aiError }}
    </div>
    @endif

    {{-- Message list --}}
    <div
        x-ref="msgList"
        class="flex-1 overflow-y-auto rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 p-4 space-y-4"
    >
        {{-- Empty state --}}
        <template x-if="messages.length === 0 && !loading">
            <div class="flex flex-col items-center justify-center h-full text-center py-10">
                <div class="rounded-full bg-violet-100 dark:bg-violet-900/30 p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-violet-500" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-1">Ask me anything about this site</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mb-6">
                    I know everything in the WPGrip database about this site: plugins, themes, server, DB tables and more.
                </p>
                <div class="grid grid-cols-1 gap-2 w-full max-w-sm">
                    <template x-for="s in suggestions" :key="s">
                        <button
                            type="button"
                            @click="fillSuggestion(s)"
                            class="text-left text-sm rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-violet-900/30 hover:border-violet-300 transition"
                            x-text="s"
                        ></button>
                    </template>
                </div>
            </div>
        </template>

        {{-- Rendered messages --}}
        <template x-for="(msg, i) in messages" :key="i">
            <div class="ai-msg">
                <template x-if="msg.role === 'user'">
                    <div class="flex justify-end">
                        <div
                            class="max-w-[78%] rounded-2xl rounded-tr-sm px-4 py-3 text-sm shadow-sm leading-relaxed"
                            style="background-color:#7c3aed; color:#ffffff;"
                            x-html="nl2br(msg.content)"
                        ></div>
                    </div>
                </template>
                <template x-if="msg.role === 'assistant'">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 rounded-full p-1.5 mt-0.5" style="background:#ede9fe;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;color:#7c3aed;" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div
                            class="ai-bubble max-w-[85%] rounded-2xl rounded-tl-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 px-4 py-3 text-sm shadow-sm prose prose-sm dark:prose-invert"
                            style="color:#1f2937;"
                            x-html="msg.html"
                        ></div>
                    </div>
                </template>
            </div>
        </template>

        {{-- Typing indicator --}}
        <template x-if="loading">
            <div class="flex items-start gap-3 ai-msg">
                <div class="flex-shrink-0 rounded-full p-1.5 mt-0.5" style="background:#ede9fe;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;color:#7c3aed;" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="rounded-2xl rounded-tl-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 px-4 py-3.5 shadow-sm">
                    <div class="flex gap-1.5 items-center" style="height:1.1rem;">
                        <span class="ai-dot inline-block w-2 h-2 rounded-full" style="background:#7c3aed;"></span>
                        <span class="ai-dot inline-block w-2 h-2 rounded-full" style="background:#7c3aed;"></span>
                        <span class="ai-dot inline-block w-2 h-2 rounded-full" style="background:#7c3aed;"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Input row: textarea with embedded send button --}}
    <template x-if="!limitReached">
        <div
            class="mt-3 flex items-end gap-2 rounded-xl border bg-white dark:bg-gray-900 px-3 py-2"
            style="border-color:#d1d5db; box-shadow:0 1px 2px rgba(0,0,0,.06); transition:border-color .15s, box-shadow .15s;"
            @focusin="$el.style.borderColor='#7c3aed'; $el.style.boxShadow='0 0 0 2px rgba(124,58,237,.25)'"
            @focusout="$el.style.borderColor='#d1d5db'; $el.style.boxShadow='0 1px 2px rgba(0,0,0,.06)'"
        >
            <textarea
                x-ref="input"
                x-model="draft"
                @keydown="handleKeydown($event)"
                @input="autoResize($el)"
                :disabled="loading"
                rows="1"
                placeholder="Ask about this site…  (Shift+Enter for new line)"
                class="flex-1 resize-none border-0 outline-none bg-transparent text-sm leading-relaxed text-gray-900 dark:text-gray-100 placeholder-gray-400 p-0 focus:ring-0"
                style="max-height:140px; overflow-y:auto;"
            ></textarea>
            <button
                type="button"
                @click="submit()"
                :disabled="loading || !draft.trim()"
                class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-lg transition-all duration-150"
                :class="(loading || !draft.trim())
                    ? 'bg-violet-300 dark:bg-violet-800 cursor-not-allowed opacity-70'
                    : 'bg-violet-600 hover:bg-violet-700 cursor-pointer'"
                title="Send"
            >
                <template x-if="!loading">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                    </svg>
                </template>
                <template x-if="loading">
                    <svg class="w-4 h-4 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
            </button>
        </div>
    </template>

    {{-- Limit reached banner --}}
    <template x-if="limitReached">
        <div class="mt-3 rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 px-4 py-3 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Monthly token limit reached</p>
                <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">Your workspace has used all 5M tokens this month. Resets on the 1st of next month.</p>
            </div>
        </div>
    </template>

    {{-- Footer: usage bar + disclaimer --}}
    <div class="mt-2 flex items-center justify-between px-1">
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-24 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden flex-shrink-0" title="Monthly token usage">
                <div
                    class="h-full rounded-full transition-all duration-300"
                    :class="usagePercent > 90 ? 'bg-red-500' : usagePercent > 70 ? 'bg-amber-500' : 'bg-violet-500'"
                    :style="'width:' + Math.min(usagePercent, 100) + '%'"
                ></div>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap" x-text="usageLabel"></span>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 truncate ml-3">
            Answers based on cached snapshot.
        </p>
    </div>


{{-- Seed data for aiChat() Alpine component --}}
<div
    id="ai-chat-seed"
    data-messages="{{ e(json_encode(array_map(fn($m) => [
        'role'    => $m['role'],
        'content' => $m['content'],
        'html'    => $m['role'] === 'assistant'
            ? (string) \Illuminate\Support\Str::of($m['content'])
                ->markdown(['html_input' => 'escape', 'allow_unsafe_links' => false])
            : '',
    ], $messages))) }}"
    data-age="{{ e($siteMdAge) }}"
    data-tokens-used="{{ $tokensUsed }}"
    data-tokens-limit="{{ $tokensLimit }}"
    style="display:none;"
></div>

@endsection
