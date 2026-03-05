<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div 
        x-data="{ selected: $wire.$entangle('{{ $getStatePath() }}') }" 
        class="flex gap-3"
    >
        {{-- Bitbucket --}}
        <button
            type="button"
            x-on:click="selected = 'bitbucket'"
            :class="selected === 'bitbucket' 
                ? 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-950/30 border-primary-300 dark:border-primary-700' 
                : 'ring-1 ring-gray-200 dark:ring-white/10 bg-white dark:bg-gray-900 border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center gap-3 px-4 py-3 rounded-xl border transition-all duration-150 flex-1 cursor-pointer"
        >
            <div class="flex-shrink-0">
                <x-icon-bitbucket class="w-7 h-7" />
            </div>
            <div class="text-left">
                <div class="text-sm font-semibold text-gray-950 dark:text-white">Bitbucket</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Atlassian Bitbucket</div>
            </div>
            <div class="ml-auto" x-show="selected === 'bitbucket'" x-cloak>
                <x-filament::icon icon="heroicon-s-check-circle" class="h-5 w-5 text-primary-500" />
            </div>
        </button>

        {{-- GitHub --}}
        <button
            type="button"
            x-on:click="selected = 'github'"
            :class="selected === 'github' 
                ? 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-950/30 border-primary-300 dark:border-primary-700' 
                : 'ring-1 ring-gray-200 dark:ring-white/10 bg-white dark:bg-gray-900 border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center gap-3 px-4 py-3 rounded-xl border transition-all duration-150 flex-1 cursor-pointer"
        >
            <div class="flex-shrink-0">
                <x-icon-github class="w-7 h-7" />
            </div>
            <div class="text-left">
                <div class="text-sm font-semibold text-gray-950 dark:text-white">GitHub</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">GitHub by Microsoft</div>
            </div>
            <div class="ml-auto" x-show="selected === 'github'" x-cloak>
                <x-filament::icon icon="heroicon-s-check-circle" class="h-5 w-5 text-primary-500" />
            </div>
        </button>
    </div>
</x-dynamic-component>
