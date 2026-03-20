@extends('site/single/pagetemplate')

@section('content')

<div
    x-data="agentChat()"
    x-init="init($el)"
    class="flex"
    style="height: calc(100vh - 220px); min-height: 520px;"
>
    {{-- ═══ History sidebar ═══ --}}
    <div
        class="agent-history-sidebar"
        x-show="showHistory && conversations.length > 0"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-x-2"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-2"
        x-cloak
    >
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-white/5">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">History</span>
            <button @click="showHistory = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto py-2 space-y-0.5">
            <template x-for="conv in conversations" :key="conv.id">
                <div
                    @click="resumeConversation(conv.id)"
                    :class="{'active': conversationId === conv.id}"
                    class="agent-history-item group"
                >
                    <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] leading-tight" x-text="conv.title"></p>
                        <p class="text-[10px] opacity-50 mt-0.5" x-text="conv.updated_at"></p>
                    </div>
                    <button
                        @click="deleteConversation(conv.id, $event)"
                        class="delete-btn"
                        title="Delete"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </div>

    {{-- ═══ Main chat area ═══ --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Header row --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                {{-- History toggle button --}}
                <button
                    @click="toggleHistory()"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-violet-50 dark:hover:bg-violet-900/30 hover:border-violet-300 transition relative"
                    :class="showHistory ? 'bg-violet-50 border-violet-300 text-violet-600 dark:bg-violet-900/30 dark:border-violet-600 dark:text-violet-400' : ''"
                    title="Chat history"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span
                        x-show="conversations.length > 0"
                        class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-violet-500 text-white text-[9px] font-bold flex items-center justify-center"
                        x-text="conversations.length"
                    ></span>
                </button>

                <x-filament::icon icon="heroicon-m-cpu-chip" class="w-5 h-5 text-violet-500" />
                <span class="font-semibold text-gray-800 dark:text-white">Agent Mode</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">
                    AI Agent
                </span>
            </div>

            <div class="flex items-center gap-2">
                {{-- Context freshness badge --}}
                <span
                    class="hidden sm:inline-flex items-center gap-1 rounded-full border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-800 px-2.5 py-1 text-xs text-gray-500 dark:text-gray-400"
                    title="Age of the cached site snapshot (SITE.md)"
                >
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
                    <span class="hidden sm:inline" x-text="refreshing ? 'Refreshing…' : 'Refresh'"></span>
                </button>

                {{-- New conversation --}}
                <button
                    @click="newConversation()"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-violet-900/30 hover:border-violet-300 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Chat
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-violet-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h9a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0015.75 4.5h-9A2.25 2.25 0 004.5 6.75v10.5A2.25 2.25 0 006.75 19.5z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-1">AI Agent Mode</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mb-2">
                        I can <strong>inspect and act</strong> on your WordPress site — update plugins, clear caches, check uptime, run WP-CLI commands, trigger backups, and more.
                    </p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mb-6">
                        ⚡ Write operations will ask for confirmation before executing.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full max-w-lg">
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
                <div class="agent-msg">
                    {{-- User message --}}
                    <template x-if="msg.role === 'user'">
                        <div class="flex justify-end">
                            <div
                                class="max-w-[78%] rounded-2xl rounded-tr-sm px-4 py-3 text-sm shadow-sm leading-relaxed bg-violet-600 text-white"
                                x-html="escapeHtml(msg.content).replace(/\n/g, '<br>')"
                            ></div>
                        </div>
                    </template>
                    {{-- Assistant message --}}
                    <template x-if="msg.role === 'assistant'">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 rounded-full p-1.5 mt-0.5 bg-violet-100 dark:bg-violet-900/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h9a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0015.75 4.5h-9A2.25 2.25 0 004.5 6.75v10.5A2.25 2.25 0 006.75 19.5z" />
                                </svg>
                            </div>
                            <div class="max-w-[85%]">
                                {{-- Tool badges --}}
                                <template x-if="msg.tools">
                                    <div class="mb-1.5 flex flex-wrap gap-1">
                                        <template x-for="t in msg.tools.split(', ')" :key="t">
                                            <span class="agent-tool-badge" x-text="t"></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="msg.steps && msg.steps > 1">
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 mb-1">
                                        <span x-text="msg.steps + ' reasoning steps'"></span>
                                    </div>
                                </template>
                                <div
                                    class="agent-bubble rounded-2xl rounded-tl-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 px-4 py-3 text-sm shadow-sm prose prose-sm dark:prose-invert max-w-none"
                                    x-html="msg.html"
                                ></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Typing / thinking indicator --}}
            <template x-if="loading">
                <div class="flex items-start gap-3 agent-msg">
                    <div class="flex-shrink-0 rounded-full p-1.5 mt-0.5 bg-violet-100 dark:bg-violet-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h9a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0015.75 4.5h-9A2.25 2.25 0 004.5 6.75v10.5A2.25 2.25 0 006.75 19.5z" />
                        </svg>
                    </div>
                    <div class="rounded-2xl rounded-tl-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 px-4 py-3.5 shadow-sm">
                        <div class="flex items-center gap-2">
                            <div class="flex gap-1.5 items-center" style="height:1.1rem;">
                                <span class="agent-dot inline-block w-2 h-2 rounded-full bg-violet-500"></span>
                                <span class="agent-dot inline-block w-2 h-2 rounded-full bg-violet-500"></span>
                                <span class="agent-dot inline-block w-2 h-2 rounded-full bg-violet-500"></span>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-500">Agent is thinking…</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Input row --}}
        <template x-if="!limitReached">
            <div
                class="mt-3 flex items-end gap-2 rounded-xl border bg-white dark:bg-gray-900 px-3 py-2 border-gray-300 dark:border-gray-700 shadow-sm transition-all duration-150 focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-500/25"
            >
                <textarea
                    x-ref="input"
                    x-model="draft"
                    @keydown="handleKeydown($event)"
                    @input="autoResize($el)"
                    :disabled="loading"
                    rows="1"
                    placeholder="Ask the agent to inspect or manage your site…  (Shift+Enter for new line)"
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
                Powered by Laravel AI SDK · Claude
            </p>
        </div>

    </div>{{-- end main chat area --}}
</div>

{{-- Seed data for agentChat() Alpine component --}}
<div
    id="agent-chat-seed"
    data-messages="{{ e(json_encode(array_map(fn($m) => [
        'role'    => $m['role'],
        'content' => $m['content'],
        'html'    => $m['html'] ?? '',
    ], $messages))) }}"
    data-age="{{ e($siteMdAge) }}"
    data-tokens-used="{{ $tokensUsed }}"
    data-tokens-limit="{{ $tokensLimit }}"
    data-conversation-id="{{ e($conversationId ?? '') }}"
    data-conversations="{{ e(json_encode($conversations)) }}"
    style="display:none;"
></div>

@endsection
