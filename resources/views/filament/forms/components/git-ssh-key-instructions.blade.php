<div x-data="{
    copiedCat: false,
    copiedGen: false,
    copy(text, field) {
        navigator.clipboard.writeText(text);
        this[field] = true;
        setTimeout(() => this[field] = false, 2000);
    }
}" class="rounded-xl border border-blue-200 bg-blue-50 dark:border-blue-700 dark:bg-blue-950/50 p-4">
    <div class="flex items-start gap-3">
        <div class="shrink-0 mt-0.5">
            <x-heroicon-o-key class="h-5 w-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div class="flex-1 min-w-0 space-y-2">
            <p class="text-sm text-blue-700 dark:text-blue-300">
                Your <strong>server user</strong> needs an SSH key added to your Git provider's <strong>Deploy Keys</strong>. SSH into your server and run:
            </p>
            <div class="flex items-center gap-2">
                <code class="flex-1 rounded bg-white dark:bg-gray-900 border border-blue-200 dark:border-blue-700 px-3 py-1.5 text-xs font-mono text-gray-700 dark:text-gray-300 select-all">cat ~/.ssh/id_rsa.pub || cat ~/.ssh/id_ed25519.pub</code>
                <button type="button" x-on:click="copy('cat ~/.ssh/id_rsa.pub || cat ~/.ssh/id_ed25519.pub', 'copiedCat')" class="shrink-0 rounded-lg p-1.5 text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-800 transition" title="Copy">
                    <svg x-show="!copiedCat" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="9" y="9" width="13" height="13" rx="2" /><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" /></svg>
                    <svg x-show="copiedCat" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </button>
            </div>
            <p class="text-xs text-blue-600 dark:text-blue-400">No key? Generate one: <code class="bg-white dark:bg-gray-900 rounded px-1 py-0.5 text-[11px]">ssh-keygen -t ed25519 -C "deploy" -N ""</code></p>
            <p class="text-xs text-blue-600 dark:text-blue-400">
                Then add the key in: <strong>GitHub</strong> &rarr; Settings &rarr; Deploy keys &nbsp;|&nbsp; <strong>Bitbucket</strong> &rarr; Settings &rarr; Access keys
            </p>
        </div>
    </div>
</div>
