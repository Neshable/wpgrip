@php
// If the header is displayed within RepositoryResource.
$site_model = $this->getRecord();
if ( isset( $isRepository ) && $isRepository )
{
    $site_model = $this->getRecord()->site;
}


$fields = array( 
    'Name' => $site_model->name ?? 'Website Overview',
    'Site URL' => $site_model->url,
);


$tenant = Filament\Facades\Filament::getTenant(); 

@endphp 

@persist('siteheader')
<div class="bg-white border-gray-200 rounded-lg shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ">

    <div class="flex flex-col items-center md:flex-row">   
    
        @php
            $screenshotPath = $this->getRecord()->screenshot_path;
            $screenshotUrl  = null;
            if ($screenshotPath) {
                // Try public disk first, fall back to s3
                foreach (['public', 's3'] as $disk) {
                    try {
                        if (Storage::disk($disk)->exists($screenshotPath)) {
                            $screenshotUrl = Storage::disk($disk)->url($screenshotPath);
                            break;
                        }
                    } catch (\Throwable) {}
                }
            }
        @endphp
        @if ($screenshotUrl)
            <img class="object-cover w-full rounded-t-lg h-28 md:h-28 md:w-auto md:rounded-none md:rounded-l-lg"
                 src="{{ $screenshotUrl }}" alt="Site screenshot">
        @endif
        
        <div class="w-full relative px-6 py-4 h-full flex-row items-center justify-between">
            <div class="flex flex-col md:flex-row md:items-center">
                <div class="pb-4 md:pb-0">
                    <div class="flex items-center gap-4 mb-2">
                       <h2 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white sm:text-3xl">{{ $fields['Name'] }}</h2>
                       {{-- <div>
                        <x-filament::icon
                            x-tooltip="{
                                content: 'Add to favorites',
                                theme: $store.theme,
                            }"
                            icon="heroicon-m-star"
                            color="gray"
                            style="text-yellow-400"
                            wire:target="search"
                            tooltip="Add to favorites"
                            class="h-5 w-5 text-gray-600 dark:text-gray-500"       
                        />
                       </div> --}}
                       @if ( $this->getRecord()->board_url )
                        <a 
                        target="_blank" 
                        href="{{ $this->getRecord()->board_url }}"
                        >
             
                        <x-filament::icon
                                x-tooltip="{
                                    content: 'Open {{ $this->getRecord()->board_provider }} board',
                                    theme: $store.theme,
                                }"
                                icon="icon-trello"
                                wire:target="search"
                                tooltip="Open {{ $this->getRecord()->board_provider }} board"
                                class="h-5 w-5 text-gray-600 dark:text-gray-500"       
                            />   
                        </a>
                        @endif
                    </div>
                    <a href="{{ $fields['Site URL'] }}" target="_blank" class="text-sm text-gray-500 mb-1 flex items-center" rel="noreferrer">
                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="w-3 mr-2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path>
                       </svg>
                       {{ $fields['Site URL'] }}
                    </a>
                    {{-- <span x-tooltip="{
                        content: 'Client {{ $this->getRecord()->client->name }}',
                        theme: $store.theme,
                    }" class="text-sm text-gray-500 flex items-center">
                        <x-filament::icon
                            icon="heroicon-m-user-group"
                            class="h-3 w-3 mr-2 text-gray-600 dark:text-gray-500"       
                        />
                        {{ $this->getRecord()->client->name }}
                     </span> --}}
                     <span x-tooltip="{
                        content: 'Server {{ $this->getRecord()->server->name }}',
                        theme: $store.theme,
                    }" class="text-sm text-gray-500 flex items-center">
                        <x-filament::icon
                            icon="heroicon-m-server-stack"
                            class="h-3 w-3 mr-2 text-gray-600 dark:text-gray-500"       
                        />
                        {{ $this->getRecord()->server->ip }}
                     </span>
                 </div>
              
               <div class="flex items-center gap-4 ml-auto">
                    <div class="flex items-center">
                        <div class="rounded-full bg-gray-100  py-1 px-2 inline-flex items-center gap-2">
                            <x-filament::icon
                                x-tooltip="{
                                    content: 'WP-CLI Version',
                                    theme: $store.theme,
                                }"
                                icon="heroicon-m-command-line"
                                tooltip="WP-CLI Version"
                                class="h-5 w-5 text-gray-600 dark:text-gray-500"       
                            />
                        <p class="text-gray-600 text-sm"><strong>{{  $site_model->cli_ver ?? '' }}</strong></p>
                        </div>
                        
                    </div>
                  <div class="rounded-full bg-gray-100 py-1 px-2 inline-flex items-center gap-2">
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -1 100 50" class="w-4 h-4" fill="currentColor">
                        <path d="m7.579 10.123 14.204 0c4.169 0.035 7.19 1.237 9.063 3.604 1.873 2.367 2.491 5.6 1.855 9.699-0.247 1.873-0.795 3.71-1.643 5.512-0.813 1.802-1.943 3.427-3.392 4.876-1.767 1.837-3.657 3.003-5.671 3.498-2.014 0.495-4.099 0.742-6.254 0.742l-6.36 0-2.014 10.07-7.367 0 7.579-38.001 0 0m6.201 6.042-3.18 15.9c0.212 0.035 0.424 0.053 0.636 0.053 0.247 0 0.495 0 0.742 0 3.392 0.035 6.219-0.3 8.48-1.007 2.261-0.742 3.781-3.321 4.558-7.738 0.636-3.71 0-5.848-1.908-6.413-1.873-0.565-4.222-0.83-7.049-0.795-0.424 0.035-0.83 0.053-1.219 0.053-0.353 0-0.724 0-1.113 0l0.053-0.053"></path>
                        <path d="m41.093 0 7.314 0-2.067 10.123 6.572 0c3.604 0.071 6.289 0.813 8.056 2.226 1.802 1.413 2.332 4.099 1.59 8.056l-3.551 17.649-7.42 0 3.392-16.854c0.353-1.767 0.247-3.021-0.318-3.763-0.565-0.742-1.784-1.113-3.657-1.113l-5.883-0.053-4.346 21.783-7.314 0 7.632-38.054 0 0"></path>
                        <path d="m70.412 10.123 14.204 0c4.169 0.035 7.19 1.237 9.063 3.604 1.873 2.367 2.491 5.6 1.855 9.699-0.247 1.873-0.795 3.71-1.643 5.512-0.813 1.802-1.943 3.427-3.392 4.876-1.767 1.837-3.657 3.003-5.671 3.498-2.014 0.495-4.099 0.742-6.254 0.742l-6.36 0-2.014 10.07-7.367 0 7.579-38.001 0 0m6.201 6.042-3.18 15.9c0.212 0.035 0.424 0.053 0.636 0.053 0.247 0 0.495 0 0.742 0 3.392 0.035 6.219-0.3 8.48-1.007 2.261-0.742 3.781-3.321 4.558-7.738 0.636-3.71 0-5.848-1.908-6.413-1.873-0.565-4.222-0.83-7.049-0.795-0.424 0.035-0.83 0.053-1.219 0.053-0.353 0-0.724 0-1.113 0l0.053-0.053"></path>
                     </svg>
                     <p class="text-gray-600 text-sm">
                      <strong>{{  $site_model->php_ver ?? '' }}</strong>
                      </p>
                  </div>
                  <div class="flex items-center">
                     <div class="rounded-full bg-green-100  py-1 px-2 inline-flex items-center gap-2">
                         <x-filament::icon
                             x-tooltip="{
                                 content: 'WordPress Core Version',
                                 theme: $store.theme,
                             }"
                             icon="icon-wordpress"
                             wire:target="search"
                             tooltip="WordPress Core Version"
                             class="h-5 w-5 text-gray-600 dark:text-gray-500"       
                         />
                        <p class="text-gray-600 text-sm"><strong>{{  $site_model->wp_ver ?? 'n/a' }}</strong></p>
                     </div>
                     
                  </div>
    
         
                  <x-filament::dropdown>
                        <x-slot name="trigger">
                            <x-filament::icon-button
                                icon="heroicon-m-ellipsis-vertical"
                                label="actions"
                            >
                    
                            </x-filament::icon-button>
                        </x-slot>
                        
                        <x-filament::dropdown.list>
                            <x-filament::dropdown.list.item wire:click="triggerSync">
                                Sync
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item wire:click="triggerScreenshot">
                                New Screenshot
                            </x-filament::dropdown.list.item>
                            
                            <x-filament::dropdown.list.item 
                            tag="a"
                            :href="route( 'filament.dashboard.resources.sites.edit', ['record' => $this->getRecord()->id ? $this->getRecord()->id : '2', 'tenant' => $tenant->uuid] )"
                            >
                                Edit
                            </x-filament::dropdown.list.item>
                            
                            <x-filament::dropdown.list.item wire:click="openDeleteModal">
                                Delete
                            </x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>
               </div>
    
               {{-- Site mode --}}
               @if ( !$this->getRecord()->getConnectionStatus() )
               <div  
                    x-tooltip="{
                        content: 'Website is using lite mode. No SSH connection is established.',
                        theme: $store.theme,
                    }"
                    class="border-red-600 rounded-tr-lg semi-bold bg-red-100 text-sm text-gray-500 dark:text-gray-800 py-1 px-2 absolute top-0 right-0">
                    Lite
                </div>
                @endif
    
            </div>
         </div>
    
         
    </div>

</div>

@endpersist

