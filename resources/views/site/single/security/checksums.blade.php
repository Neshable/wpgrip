<x-filament::page>
    @include('filament/menus/internal-site-menu-include')
    

    <div class="grid grid-cols-12 gap-8">
        <div class="sm:col-span-12 md:col-span-3">
            @include('filament/menus/internal-site-menu-vertical')
        </div>
        <div class="sm:col-span-12 md:col-span-9 flex flex-col gap-y-8">
        
        <x-filament::section icon="plugins"  icon-color="warning">     
            <x-slot name="heading">
                Checksums
            </x-slot>
            <x-slot name="headerEnd">           
                <x-filament::link icon="heroicon-m-wrench" size="xl" color="danger" badge-color="danger">
                    Resolve <x-slot name="badge">
                        3
                    </x-slot>
                </x-filament::link>
            </x-slot>
            If you don't need these plugins, you should consider removing them. Deactivated plugins can still provide a way for a hacker to gain entry because the code may still be publicly accessible.
        </x-filament::section>
        
     
        
    
        </div>
    </div>

   
    
  
</x-filament::page>