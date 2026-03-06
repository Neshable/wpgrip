<div class="rounded-xl border border-blue-200 bg-blue-50 dark:border-blue-700 dark:bg-blue-950/50 p-4">
    <div class="flex items-start gap-3">
        <div class="shrink-0 mt-0.5">
            <x-heroicon-o-key class="h-6 w-6 text-blue-600 dark:text-blue-400" />
        </div>
        <div class="flex-1 min-w-0 space-y-3">
            <div>
                <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200">
                    Git SSH Key Setup Required
                </h3>
                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                    For deployments to work, your <strong>server</strong> needs permission to pull from your Git repository. You must copy the <strong>server user's SSH public key</strong> and add it to your repository's <strong>Access Keys</strong> (also called Deploy Keys).
                </p>
            </div>

            <div class="rounded-lg bg-white dark:bg-gray-900 border border-blue-200 dark:border-blue-700 p-3 space-y-3">
                <p class="text-xs font-medium text-blue-700 dark:text-blue-300">Step 1 — Get the server user's public key</p>
                <p class="text-xs text-blue-600 dark:text-blue-400">SSH into your server as the site's user and run:</p>
                <div x-data="{ copiedCmd: false }" class="relative">
                    <code class="block rounded bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-2 text-xs font-mono text-gray-700 dark:text-gray-300 select-all">cat ~/.ssh/id_rsa.pub</code>
                    <button
                        type="button"
                        x-on:click="
                            navigator.clipboard.writeText('cat ~/.ssh/id_rsa.pub');
                            copiedCmd = true;
                            setTimeout(() => copiedCmd = false, 2000);
                        "
                        class="absolute top-1.5 right-1.5 rounded-lg p-1 text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-800 transition"
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
                <p class="text-xs text-blue-600 dark:text-blue-400">If no key exists, generate one first:</p>
                <div x-data="{ copiedGen: false }" class="relative">
                    <code class="block rounded bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-2 text-xs font-mono text-gray-700 dark:text-gray-300 select-all">ssh-keygen -t ed25519 -C "deploy" -f ~/.ssh/id_ed25519 -N ""</code>
                    <button
                        type="button"
                        x-on:click="
                            navigator.clipboard.writeText('ssh-keygen -t ed25519 -C \"deploy\" -f ~/.ssh/id_ed25519 -N \"\"');
                            copiedGen = true;
                            setTimeout(() => copiedGen = false, 2000);
                        "
                        class="absolute top-1.5 right-1.5 rounded-lg p-1 text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-800 transition"
                        title="Copy command"
                    >
                        <template x-if="!copiedGen">
                            <x-heroicon-o-clipboard-document class="h-4 w-4" />
                        </template>
                        <template x-if="copiedGen">
                            <x-heroicon-o-clipboard-document-check class="h-4 w-4 text-green-600 dark:text-green-400" />
                        </template>
                    </button>
                </div>
            </div>

            <div class="rounded-lg bg-white dark:bg-gray-900 border border-blue-200 dark:border-blue-700 p-3 space-y-2">
                <p class="text-xs font-medium text-blue-700 dark:text-blue-300">Step 2 — Add the key to your Git provider</p>
                <p class="text-xs text-blue-600 dark:text-blue-400">Copy the public key output and add it as a <strong>Deploy Key</strong> or <strong>Access Key</strong> in your repository settings:</p>
                <ul class="text-xs text-blue-600 dark:text-blue-400 list-disc list-inside space-y-1 ml-1">
                    <li><strong>GitHub</strong> → Repository → Settings → Deploy keys → Add deploy key</li>
                    <li><strong>Bitbucket</strong> → Repository → Settings → Access keys → Add key</li>
                </ul>
                <p class="text-xs text-blue-500 dark:text-blue-500 mt-1 italic">Read access is sufficient for deployments.</p>
            </div>
        </div>
    </div>
</div>
