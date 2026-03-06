<div x-data="{
    copiedKey: false,
    copiedCmd: false,
    copy(text, field) {
        navigator.clipboard.writeText(text);
        this[field] = true;
        setTimeout(() => this[field] = false, 2000);
    }
}" class="rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-700 dark:bg-amber-950/50 p-4">
    <div class="flex items-start gap-3">
        <div class="shrink-0 mt-0.5">
            <x-heroicon-o-key class="h-5 w-5 text-amber-600 dark:text-amber-400" />
        </div>
        <div class="flex-1 min-w-0 space-y-2">
            <p class="text-sm text-amber-700 dark:text-amber-300">
                <strong>SSH Key Setup:</strong> Copy the key below and add it to <code class="rounded bg-amber-100 px-1 py-0.5 font-mono text-xs dark:bg-amber-900">~/.ssh/authorized_keys</code> on your server for the SSH user.
            </p>

            @if($sshPublicKey)
                <div class="flex items-start gap-2">
                    <pre class="flex-1 rounded-lg bg-white dark:bg-gray-900 border border-amber-200 dark:border-amber-700 p-2.5 text-xs font-mono text-gray-700 dark:text-gray-300 overflow-x-auto whitespace-pre-wrap break-all select-all">{{ $sshPublicKey }}</pre>
                    <button type="button" x-on:click="copy(@js($sshPublicKey), 'copiedKey')" class="shrink-0 rounded-lg p-1.5 text-amber-600 hover:bg-amber-100 dark:text-amber-400 dark:hover:bg-amber-800 transition" title="Copy key">
                        <svg x-show="!copiedKey" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="9" y="9" width="13" height="13" rx="2" /><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" /></svg>
                        <svg x-show="copiedKey" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <code class="flex-1 rounded bg-white dark:bg-gray-900 border border-amber-200 dark:border-amber-700 px-2.5 py-1.5 text-[11px] font-mono text-gray-600 dark:text-gray-400 select-all">echo {{ json_encode($sshPublicKey) }} >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys</code>
                    <button type="button" x-on:click="copy('echo ' + @js(json_encode($sshPublicKey)) + ' >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys', 'copiedCmd')" class="shrink-0 rounded-lg p-1.5 text-amber-600 hover:bg-amber-100 dark:text-amber-400 dark:hover:bg-amber-800 transition" title="Copy command">
                        <svg x-show="!copiedCmd" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="9" y="9" width="13" height="13" rx="2" /><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" /></svg>
                        <svg x-show="copiedCmd" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </button>
                </div>
                <p class="text-xs text-amber-600 dark:text-amber-500">Run the command above as the SSH user on your server.</p>
            @else
                <p class="text-sm text-red-600 dark:text-red-400">
                    <x-heroicon-o-exclamation-triangle class="inline h-4 w-4 -mt-0.5" />
                    No SSH key found for your account. Please contact support.
                </p>
            @endif
        </div>
    </div>
</div>
