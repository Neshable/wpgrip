@extends('site/single/pagetemplate')

@section('content')

{{-- Styles injected into <head> via Filament render hook in AiAssistant.php --}}

<div
    x-data="aiChat()"
    x-init="init($el)"
    class="flex flex-col"
    style="height: calc(100vh - 220px); min-height: 520px;"
>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-sparkles" class="w-5 h-5 text-violet-500" />
            <span class="font-semibold text-gray-800 dark:text-white">AI Assistant</span>
            <span class="text-xs text-gray-400 dark:text-gray-500">Powered by Claude</span>
        </div>
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
                    I have full DB context and can connect via SSH to read files and logs.
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
                {{-- User bubble --}}
                <template x-if="msg.role === 'user'">
                    <div class="flex justify-end">
                        <div
                            class="max-w-[78%] rounded-2xl rounded-tr-sm px-4 py-3 text-sm shadow-sm leading-relaxed"
                            style="background-color:#7c3aed; color:#ffffff;"
                            x-html="nl2br(msg.content)"
                        ></div>
                    </div>
                </template>
                {{-- Assistant bubble --}}
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

    {{-- Input row --}}
    <div class="mt-3 flex items-end gap-2">
        <div class="flex-1">
            <textarea
                x-ref="input"
                x-model="draft"
                @keydown="handleKeydown($event)"
                @input="autoResize($el)"
                :disabled="loading"
                rows="1"
                placeholder="Ask about this site…  (Shift+Enter for new line)"
                style="
                    width:100%; resize:none; border-radius:.75rem;
                    border:1px solid #d1d5db; padding:.75rem 1rem;
                    font-size:.875rem; line-height:1.5;
                    background:#ffffff; color:#111827;
                    max-height:140px; overflow-y:auto;
                    box-shadow:0 1px 2px rgba(0,0,0,.06);
                    transition:border-color .15s, box-shadow .15s;
                    outline:none;
                "
                @focus="$el.style.borderColor='#7c3aed'; $el.style.boxShadow='0 0 0 2px rgba(124,58,237,.25)'"
                @blur="$el.style.borderColor='#d1d5db'; $el.style.boxShadow='0 1px 2px rgba(0,0,0,.06)'"
            ></textarea>
        </div>
        <button
            type="button"
            @click="submit()"
            :disabled="loading || !draft.trim()"
            style="
                flex-shrink:0; display:flex; align-items:center; justify-content:center;
                width:2.75rem; height:2.75rem; border-radius:.75rem;
                border:none; cursor:pointer;
                transition:background .15s, opacity .15s;
            "
            :style="(loading || !draft.trim())
                ? 'background:#c4b5fd; cursor:not-allowed;'
                : 'background:#7c3aed;'"
            @mouseover="if(!loading && draft.trim()) $el.style.background='#6d28d9'"
            @mouseout="$el.style.background=(loading || !draft.trim()) ? '#c4b5fd' : '#7c3aed'"
        >
            <template x-if="!loading">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:1.1rem;height:1.1rem;color:#fff;" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                </svg>
            </template>
            <template x-if="loading">
                <svg style="width:1.1rem;height:1.1rem;color:#fff;" class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
        </button>
    </div>

    <p class="mt-2 text-xs text-center" style="color:#9ca3af;">
        AI may make mistakes. Always verify critical information before applying changes.
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
    style="display:none;"
></div>

@endsection
