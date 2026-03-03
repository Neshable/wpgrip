<x-filament-panels::page>

    <div class="flex items-start justify-between mb-2">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Every action taken by you and your team — plugin updates, deployments, backups, invitations and more.
            </p>
        </div>
        {{-- Live refresh indicator --}}
        <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Live
        </div>
    </div>

    {{ $this->table }}

</x-filament-panels::page>
