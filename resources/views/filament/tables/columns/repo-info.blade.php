<div class="fi-ta-text grid gap-y-1 py-1 flex disabled:pointer-events-none ">    
    <div class="group relative inline-flex items-center gap-2 cursor-pointer text-sm transition-all rounded-full pr-2">
        <div class="rounded-full">
            <x-filament::icon
                icon="heroicon-m-globe-alt"
                class="h-5 w-5 text-gray-500 dark:text-gray-400" />
        </div>
        <div x-tooltip="{
            content: 'Click to copy',
            theme: $store.theme,
        }" x-on:click="
        window.navigator.clipboard.writeText('{{ $getRecord()->remote }}')
        $tooltip('Copied remote', { timeout: 2000 })" class="flex max-w-max cursor-pointer">
        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white  " style="">        
                    <div>
                        <span class="text-xs font-bold">Remote: </span> {{ $getRecord()->remote }}
                    </div>
                </div>
        </div>
        
    </div>

    <div class="group relative inline-flex items-center gap-2 cursor-pointer text-sm transition-all rounded-full pr-2">
        <div class="rounded-full">
            <x-filament::icon
                icon="heroicon-m-folder"
                class="h-5 w-5 text-gray-500 dark:text-gray-400" />        
        </div>
        <div x-tooltip="{
            content: 'Click to copy',
            theme: $store.theme,
        }" x-on:click="
        window.navigator.clipboard.writeText('{{ $getRecord()->path }}')
        $tooltip('Copied path', { timeout: 2000 })" class="flex max-w-max cursor-pointer">
        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white  " style="">        
                    <div>
                        <span class="text-xs font-bold">Path:</span> {{ $getRecord()->path }}
                    </div>
                </div>
        </div>
        
    </div> 

  


</div>