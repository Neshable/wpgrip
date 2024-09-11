

<div style="min-width: 200px;" class="whitespace-normal">
    <p class="font-semibold">{{ $getRecord()->title }}</p>
    <div class="flex items-center gap-2 text-sm mt-2">
        @if ( $getRecord()->update_version )
        <span class="inline-flex items-baseline">
            {{-- <x-filament::icon
            x-tooltip="{
                content: 'Update available',
                theme: $store.theme,
            }"
            icon="heroicon-m-arrow-path"
            class="h-5 w-5 bg-blue-50 border-s-4 border-blue-200 text-success-500 dark:text-success-400"   
            /> --}}


            <x-filament::modal  width="xl" icon="icon-plugins" :close-by-clicking-away="false">
               
                <x-slot name="trigger">
                    <div class="px-4 py-1 bg-amber-50 border-s-4 border-amber-200">
                    
                         Update to {{ $getRecord()->update_version }} 
                
                    </div>
                   
                </x-slot>
            

                <x-slot name="heading">
                    Plugin update process
                </x-slot>
            
                <x-slot name="description">
                    Updating the plugin {{ $getRecord()->title }} to version {{ $getRecord()->update_version }} 
                </x-slot>

                <x-filament::button badge-color="success" size="lg" color="info" wire:click="mountTableAction('Update', '{{ $getRecord()->id }}')">
                    Proceed with the update
                    <x-slot name="badge">
                        ver.{{ $getRecord()->update_version }} 
                    </x-slot>
                </x-filament::button>

                <div wire:loading.delay.longer wire:target="mountTableAction('Update', '{{ $getRecord()->id }}')">
                    Updating the plugin ... 
                </div>
            
            {{-- <x-slot name="footer">
                Backup is advised before making updares.
            </x-slot> --}}
                
            </x-filament::modal>

        </span>
        
        @endif
    </div>
</div>