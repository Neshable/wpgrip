
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
    

    <a href="{{ $getRecord()->url ?? '#' }}" class="text-sm text-gray-500 dark:text-gray-400">{{ $getRecord()->url ?? 'n/a' }}</a>
</div>