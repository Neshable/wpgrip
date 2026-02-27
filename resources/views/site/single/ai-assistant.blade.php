@extends('site/single/pagetemplate')

@section('content')

<div
    class="flex flex-col"
    style="height: calc(100vh - 220px); min-height: 500px;"
>

    {{-- Header bar --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-sparkles" class="w-5 h-5 text-violet-500" />
            <span class="font-semibold text-gray-800 dark:text-white">AI Assistant</span>
            <span class="text-xs text-gray-400 dark:text-gray-500">Powered by Claude</span>
        </div>
        <x-filament::button
            wire:click="clearChat"
            size="sm"
            color="gray"
            outlined
            icon="heroicon-m-trash"
        >
            Clear chat
        </x-filament::button>
    </div>

    {{-- Error banner --}}
    @if ($aiError)
    <div class="mb-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300">
        {{ $aiError }}
    </div>
    @endif

    {{-- Message list --}}
    <div
        id="ai-messages"
        class="flex-1 overflow-y-auto rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-900 p-4 space-y-4"
    >
        @if (empty($messages))
            <div class="flex flex-col items-center justify-center h-full text-center py-12">
                <div class="rounded-full bg-violet-100 dark:bg-violet-900/40 p-4 mb-4">
                    <x-filament::icon icon="heroicon-m-sparkles" class="w-8 h-8 text-violet-500" />
                </div>
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-1">Ask me anything about this site</h3>
                <p class="text-sm text-gray-400 dark:text-gray-500 max-w-xs">
                    I can answer questions using the database info and, when needed, connect via SSH to read files and logs.
                </p>
                <div class="mt-6 grid grid-cols-1 gap-2 w-full max-w-md">
                    @foreach ([
                        'List all plugins with pending updates',
                        'What PHP version is running?',
                        'Show me the last 20 lines of the error log',
                        'Check wp-config.php for any issues',
                        'How big is the database?',
                    ] as $suggestion)
                    <button
                        type="button"
                        wire:click="$set('userMessage', '{{ $suggestion }}')"
                        class="text-left text-sm rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-violet-900/30 hover:border-violet-300 dark:hover:border-violet-600 transition"
                    >
                        {{ $suggestion }}
                    </button>
                    @endforeach
                </div>
            </div>
        @else
            @foreach ($messages as $msg)
                @if ($msg['role'] === 'user')
                    <div class="flex justify-end">
                        <div class="max-w-[80%] rounded-2xl rounded-tr-sm bg-violet-600 text-white px-4 py-3 text-sm shadow-sm">
                            {!! nl2br(e($msg['content'])) !!}
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 rounded-full bg-violet-100 dark:bg-violet-900/40 p-1.5">
                            <x-filament::icon icon="heroicon-m-sparkles" class="w-4 h-4 text-violet-500" />
                        </div>
                        <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 px-4 py-3 text-sm text-gray-800 dark:text-gray-200 shadow-sm prose prose-sm dark:prose-invert">
                            {!! \Illuminate\Support\Str::of($msg['content'])->markdown(['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
                        </div>
                    </div>
                @endif
            @endforeach

            @if ($loading)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 rounded-full bg-violet-100 dark:bg-violet-900/40 p-1.5">
                        <x-filament::icon icon="heroicon-m-sparkles" class="w-4 h-4 text-violet-500" />
                    </div>
                    <div class="rounded-2xl rounded-tl-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 px-4 py-3 shadow-sm">
                        <div class="flex gap-1 items-center h-5">
                            <span class="w-2 h-2 rounded-full bg-violet-400 animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 rounded-full bg-violet-400 animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 rounded-full bg-violet-400 animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Input row --}}
    <div class="mt-3 flex items-end gap-2">
        <div class="flex-1">
            <textarea
                wire:model="userMessage"
                rows="2"
                placeholder="Ask about this site… (Shift+Enter for new line)"
                class="w-full resize-none rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-4 py-3 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:focus:ring-violet-600 transition shadow-sm"
                x-on:keydown.enter="if(!$event.shiftKey){ $event.preventDefault(); $wire.sendMessage(); }"
                style="max-height: 140px; overflow-y: auto;"
                @if ($loading) disabled @endif
            ></textarea>
        </div>
        <button
            type="button"
            wire:click="sendMessage"
            wire:loading.attr="disabled"
            @if ($loading) disabled @endif
            class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-violet-600 hover:bg-violet-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-sm"
        >
            <span wire:loading.remove wire:target="sendMessage">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                </svg>
            </span>
            <span wire:loading wire:target="sendMessage">
                <svg class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </button>
    </div>

    <p class="mt-2 text-xs text-center text-gray-400 dark:text-gray-600">
        AI may make mistakes. Always verify critical changes before applying them.
    </p>

</div>

{{-- Auto-scroll to bottom when messages update --}}
<script>
    document.addEventListener('livewire:updated', function () {
        const el = document.getElementById('ai-messages');
        if (el) el.scrollTop = el.scrollHeight;
    });
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('ai-messages');
        if (el) el.scrollTop = el.scrollHeight;
    });
</script>

@endsection
