@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp

<div class="flex flex-col items-center bg-white border-gray-200 rounded-lg shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:flex-row">   
    <div class="w-full px-6 py-4 h-full flex-row items-center justify-between">
        <div class="flex items-center">
            <div>
                <div class="flex items-center gap-4 mb-2">
                   <h2 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white sm:text-3xl">{{ $this->getRecord()->name }}</h2>
                </div>
                <div class="grid gap-y-2 py-2">
               
                    <div class="text-sm text-gray-500 inline-flex items-center" rel="noreferrer">
                        <x-filament::icon
                            x-tooltip="{
                                content: 'Git repo remote origin',
                                theme: $store.theme,
                            }"
                            icon="heroicon-m-link"
                            tooltip="Git repo remote origin"
                            class="h-5 w-5  mr-2 text-gray-600 dark:text-gray-500"       
                        />
                        {{ $this->getRecord()->remote }}
                    </div>

                    <div class="text-sm text-gray-500 inline-flex items-center" rel="noreferrer">
                        <x-filament::icon
                            x-tooltip="{
                                content: 'Provider',
                                theme: $store.theme,
                            }"
                            icon="icon-git"
                            tooltip="Provider"
                            class="h-5 w-5 mr-2 text-gray-600 dark:text-gray-500"       
                        />
                        {{ $this->getRecord()->provider }}
                    </div>
             
                </div>
 
             </div>
          
           <div class="flex items-center gap-4 ml-auto">
               
                {{-- @if(auth()->user()->can('update Repository')) --}}
                <x-filament::button
                    :href="route( 'filament.dashboard.resources.repositories.edit', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )" 
                    tag="a"
                    color="gray"
                    icon="heroicon-m-cog-6-tooth"
                    :active="request()->getRequestUri() === \URL::route('filament.dashboard.resources.repositories.edit', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid ], false)"
                >
                Settings
                </x-filament::button>
                {{-- @endif --}}
       
           </div>
        </div>
     </div>
</div>

