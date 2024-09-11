

<div style="min-width: 150px;" class="whitespace-normal">
    <div class="gap-x-2 px-3 py-4 inline-flex items-center">     
        <div class="text-md font-bold text-gray-500">
            {{ $getRecord()->version }} 
        </div>
        @if ( $getRecord()->vulnerabilities )
        
        <x-filament::modal  width="4xl">
            <x-slot name="trigger">
                <x-filament::icon-button
                    icon="heroicon-m-shield-exclamation"
                    color="danger"
                    label="Vulnerabilities found"
                />
            </x-slot>
        
            <x-slot name="heading">
                Some vulnerabilities found in this version
            </x-slot>

            <x-slot name="description">
                We detected that the current version ({{ $getRecord()->version }} ) of the plugin has some vulnerabilities. An update is strongly recommended.
            </x-slot>
        
            @php
                $vulnerabilities = $getRecord()->vulnerabilities;
                $vulnerabilities = json_decode( $vulnerabilities );
            @endphp
        
            @if ( $vulnerabilities && !empty( $vulnerabilities) )
            @php
                $formatted_vulnerabilities = $this->check_vulnerability_database($vulnerabilities, $getRecord()->version );
            @endphp
                @if ( $formatted_vulnerabilities )
                <div>
                    <h2>{{ count($formatted_vulnerabilities) }} vulnerabilities found</h2>
        
                
                    @foreach ( $formatted_vulnerabilities as $vulnerabily )
                    
                    <div class="">
                        <h3 class="fi-no-notification-title text-sm font-medium text-gray-950 dark:text-white">
                            {{ $vulnerabily['name'] }}
                        </h3>
                    
                        <p class="fi-no-notification-body text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $vulnerabily['description'] }}
                        </p>
                    </div>
        
                    @endforeach
                </div>
                @endif
            @endif
        </x-filament::modal>
       
        @endif
        
    </div>
    
    <div class="flex items-center gap-2 text-sm mt-2">
        @if ( $getRecord()->update_version )
        <span class="inline-flex items-baseline">

            <x-filament::modal  width="xl" icon="icon-plugins" :close-by-clicking-away="false">
               
                <x-slot name="trigger">
                    <div class="px-1 py-1">
                    
                         New Version {{ $getRecord()->update_version }} 
                
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

                {{ $getRecord()->vulnerabilities }}

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