
<div class="fi-ta-text grid gap-y-1 px-3 py-4">
    <div class="">                  
        <div class="flex max-w-max">
            <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white  " style="">
                <div>
                    {{ $getRecord()->name ?? 'n/a' }}
                </div>
                @if ( $getRecord()->is_staging )
                <x-filament::badge size="sm" color="gray">
                    Staging
                </x-filament::badge>
                @else
                    <x-filament::badge size="sm" color="info">
                        Production
                    </x-filament::badge>
                @endif
            </div>
        </div>
    </div>
    

    <a target="_blank" href="{{ $getRecord()->url ?? '#' }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline ">
        
        <x-filament::icon
            icon="heroicon-m-arrow-top-right-on-square"
            class="h-4 w-4 inline-block text-gray-400 dark:text-gray-500"
        />
        {{ $getRecord()->url ?? 'n/a' }}
    
    </a>
</div>