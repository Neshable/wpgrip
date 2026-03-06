<div class="rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-700 dark:bg-amber-950/50 p-4">
    <div class="flex items-start gap-3">
        <div class="shrink-0 mt-0.5">
            <x-heroicon-o-key class="h-6 w-6 text-amber-600 dark:text-amber-400" />
        </div>
        <div class="flex-1 min-w-0 space-y-3">
            <div>
                <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200">
                    SSH Key Setup Required
                </h3>
                <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                    Before adding a site, you need to authorize WPGrip to connect to your server. Copy the public SSH key below and add it to the <code class="rounded bg-amber-100 px-1.5 py-0.5 font-mono text-xs dark:bg-amber-900">~/.ssh/authorized_keys</code> file of the SSH user on your server.
                </p>
            </div>

            @if($sshPublicKey)
                <div x-data="{ copied: false }" class="space-y-2">
                    <label class="block text-xs font-medium text-amber-700 dark:text-amber-300">Your account public SSH key:</label>
                    <div class="relative">
                        <pre class="rounded-lg bg-white dark:bg-gray-900 border border-amber-200 dark:border-amber-700 p-3 pr-12 text-xs font-mono text-gray-700 dark:text-gray-300 overflow-x-auto whitespace-pre-wrap break-all select-all">{{ $sshPublicKey }}</pre>
                        <button
                            type="button"
                            x-on:click="
                                navigator.clipboard.writeText(@js($sshPublicKey));
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            "
                            class="absolute top-2 right-2 rounded-lg p-1.5 text-amber-600 hover:bg-amber-100 dark:text-amber-400 dark:hover:bg-amber-800 transition"
                            title="Copy to clipboard"
                        >
                            <template x-if="!copied">
                                <x-heroicon-o-clipboard-document class="h-4 w-4" />
                            </template>
                            <template x-if="copied">
                                <x-heroicon-o-clipboard-document-check class="h-4 w-4 text-green-600 dark:text-green-400" />
                            </template>
                        </button>
                    </div>
                </div>

                <div class="rounded-lg bg-white dark:bg-gray-900 border border-amber-200 dark:border-amber-700 p-3">
                    <p class="text-xs font-medium text-amber-700 dark:text-amber-300 mb-2">Quick setup via terminal:</p>
                    <div x-data="{ copiedCmd: false }" class="relative">
                        <code class="block text-xs font-mono text-gray-600 dark:text-gray-400 break-all select-all">echo {{ json_encode($sshPublicKey) }} >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys</code>
                        <button
                            type="button"
                            x-on:click="
                                navigator.clipboard.writeText('echo ' + @js(json_encode($sshPublicKey)) + ' >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys');
                                copiedCmd = true;
                                setTimeout(() => copiedCmd = false, 2000);
                            "
                            class="absolute top-0 right-0 rounded-lg p-1 text-amber-600 hover:bg-amber-100 dark:text-amber-400 dark:hover:bg-amber-800 transition"
                            title="Copy command"
                        >
                            <template x-if="!copiedCmd">
                                <x-heroicon-o-clipboard-document class="h-4 w-4" />
                            </template>
                            <template x-if="copiedCmd">
                                <x-heroicon-o-clipboard-document-check class="h-4 w-4 text-green-600 dark:text-green-400" />
                            </template>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-amber-600 dark:text-amber-500">Run this command as the SSH user you'll specify below on your server.</p>
                </div>
            @else
                <div class="rounded-lg bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-700 p-3">
                    <p class="text-sm text-red-600 dark:text-red-400">
                        <x-heroicon-o-exclamation-triangle class="inline h-4 w-4 -mt-0.5" />
                        No SSH key found for your account. Please contact support.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
