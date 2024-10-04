
<div class="fi-ta-text grid gap-y-1 py-1">  
    

    <div class="group relative inline-flex items-center gap-2 cursor-pointer text-sm transition-all rounded-full px-2 py-1">
        <div class="rounded-full bg-white">
            <x-filament::icon
                x-tooltip="{
                    content: 'WordPress Core Version',
                    theme: $store.theme,
                }"
                icon="icon-wordpress"
                wire:target="search"
                tooltip="WordPress Core Version"
                class="h-4 w-5"       
            />
        </div>
        <span class="font-bold">{{ $getRecord()->wp_ver ?? 'n/a' }}</span>
    </div>
   
    <div class="group relative inline-flex items-center gap-2 cursor-pointer text-sm transition-all rounded-full px-2 py-1">
        <div class="rounded-full bg-white">
            <x-filament::icon
                x-tooltip="{
                    content: 'PHP Version',
                    theme: $store.theme,
                }"
                icon="icon-php"
                tooltip="PHP Version"
                class="h-4 w-5"       
            />
        </div>
        <span class="font-bold">{{ $getRecord()->php_ver ?? 'n/a' }}</span>
    </div> 
</div>
