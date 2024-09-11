
<div class="flex items-center">
    <div class="rounded-full {{ \App\Services\Helpers\BladeHelper::getWPVersionBackgroundClass( $getRecord()->wp_ver ?? null ) }} py-1 px-2 inline-flex items-center gap-2 dark:bg-transparent">
        <x-filament::icon
            x-tooltip="{
                content: 'WordPress Core Version',
                theme: $store.theme,
            }"
            icon="icon-wordpress"
            wire:target="search"
            tooltip="WordPress Core Version"
            class="h-5 w-5 text-gray-600 dark:text-gray-200"       
        />
       <p class="text-gray-600 dark:text-gray-200 text-sm"><strong>{{ $getRecord()->wp_ver ?? 'n/a' }}</strong></p>
    </div>
    
 </div>
