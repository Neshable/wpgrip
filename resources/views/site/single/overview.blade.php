@extends('site/single/pagetemplate')

@section('content')

    @include('site/notifications/general')

    @php

        $fields = [
            'Name' => isset($this->getRecord()->name) ? $this->getRecord()->name : 'Website Overview',
            'Last sync' => isset($this->getRecord()->updated_at) ? $this->getRecord()->updated_at : 'n/a',
            'WordPress Version' => $this->getRecord()->wp_ver,
            'PHP Version' => $this->getRecord()->php_ver,
            'Directory Size' => $this->getRecord()->getFormatedDBSize() ?? 'n/a',
            'Database Prefix' => $this->getRecord()->db_prefix ?? 'n/a',
            'Database Size' => $this->getRecord()->getDBSize() ?? 'n/a',
        ];

        $additional_fields = [
            'Domain Expire Date' => isset($this->getRecord()->sitemeta->domain_expiry_date) 
                    ? \Carbon\Carbon::parse($this->getRecord()->sitemeta->domain_expiry_date)->format('d F Y') 
                    : 'n/a',
            'Site URL' => $this->getRecord()->url,
            'Client name' => isset($this->getRecord()->client) ? $this->getRecord()->client->name : 'Client',
            'Server IP' => $this->getRecord()->server->ip,
            'Server Name' => $this->getRecord()->server->name,
            'Server Provider' => $this->getRecord()->server->provider,
        ];

    @endphp

    {{-- <x-filament::modal x-init="setTimeout(() => { $dispatch('open-modal', { id: 'user_notification' }) }, 2000)"
            alignment="center"
            icon="heroicon-o-information-circle" id="user_notification">

            <x-slot name="heading">
                Modal heading
            </x-slot>
        
            <x-slot name="description">
                Modal description
            </x-slot>

            <x-slot name="footer">
                Footer
            </x-slot>
        </x-filament::modal> --}}



    {{-- <div class="flex flex-col items-center bg-white border-gray-200 rounded-lg  md:flex-row">
        @if ($this->getRecord()->screenshot_path && Storage::disk('local')->exists($this->getRecord()->screenshot_path))
            <img class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-96 md:rounded-none md:rounded-l-lg" 
            src="{{ Storage::url($this->getRecord()->screenshot_path) }}" alt="">
        @endif 
       
        <div class="w-full p-4 h-full flex-row items-center justify-between">
            <div class="flex items-center justify-between w-full">
               <div>
                  <div class="flex items-center gap-4 mb-2">
                     <h2 class="text-lg leading-6 text-gray-900 font-semibold">{{ $fields['Name'] }}</h2>
                     <div class="border-yellow-400 flex items-center gap-2 border rounded-full bg-white px-2 cursor-pointer hover:border-yellow-400" data-tip="true" data-for="favorite" currentitem="false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-4 h-4 text-yellow-400">
                           <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-gray-600 text-sm">Favorite</p>
                     </div>
                  </div>
                  <a href="{{ $fields['Site URL'] }}" target="_blank" class="text-sm text-gray-500 inline-flex items-center" rel="noreferrer">
                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="w-3 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path>
                     </svg>
                     {{ $fields['Site URL'] }}
                  </a>
               </div>
              
            </div>
            <div class="flex items-start mt-8">
               <div class="flex items-center gap-2 flex-wrap w-3/4">
                  Something here.
               </div>
               <div class="flex items-center gap-4 ml-auto">

              

                
                  <div class="rounded-full bg-gray-100 py-1 px-2 inline-flex items-center gap-2">
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -1 100 50" class="w-4 h-4" fill="currentColor">
                        <path d="m7.579 10.123 14.204 0c4.169 0.035 7.19 1.237 9.063 3.604 1.873 2.367 2.491 5.6 1.855 9.699-0.247 1.873-0.795 3.71-1.643 5.512-0.813 1.802-1.943 3.427-3.392 4.876-1.767 1.837-3.657 3.003-5.671 3.498-2.014 0.495-4.099 0.742-6.254 0.742l-6.36 0-2.014 10.07-7.367 0 7.579-38.001 0 0m6.201 6.042-3.18 15.9c0.212 0.035 0.424 0.053 0.636 0.053 0.247 0 0.495 0 0.742 0 3.392 0.035 6.219-0.3 8.48-1.007 2.261-0.742 3.781-3.321 4.558-7.738 0.636-3.71 0-5.848-1.908-6.413-1.873-0.565-4.222-0.83-7.049-0.795-0.424 0.035-0.83 0.053-1.219 0.053-0.353 0-0.724 0-1.113 0l0.053-0.053"></path>
                        <path d="m41.093 0 7.314 0-2.067 10.123 6.572 0c3.604 0.071 6.289 0.813 8.056 2.226 1.802 1.413 2.332 4.099 1.59 8.056l-3.551 17.649-7.42 0 3.392-16.854c0.353-1.767 0.247-3.021-0.318-3.763-0.565-0.742-1.784-1.113-3.657-1.113l-5.883-0.053-4.346 21.783-7.314 0 7.632-38.054 0 0"></path>
                        <path d="m70.412 10.123 14.204 0c4.169 0.035 7.19 1.237 9.063 3.604 1.873 2.367 2.491 5.6 1.855 9.699-0.247 1.873-0.795 3.71-1.643 5.512-0.813 1.802-1.943 3.427-3.392 4.876-1.767 1.837-3.657 3.003-5.671 3.498-2.014 0.495-4.099 0.742-6.254 0.742l-6.36 0-2.014 10.07-7.367 0 7.579-38.001 0 0m6.201 6.042-3.18 15.9c0.212 0.035 0.424 0.053 0.636 0.053 0.247 0 0.495 0 0.742 0 3.392 0.035 6.219-0.3 8.48-1.007 2.261-0.742 3.781-3.321 4.558-7.738 0.636-3.71 0-5.848-1.908-6.413-1.873-0.565-4.222-0.83-7.049-0.795-0.424 0.035-0.83 0.053-1.219 0.053-0.353 0-0.724 0-1.113 0l0.053-0.053"></path>
                     </svg>
                     <p class="text-gray-600 text-sm">
                      <strong>{{ $this->getRecord()->php_ver ?? '' }}</strong>
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
                        <p class="text-gray-600 text-sm"><strong>{{ $this->getRecord()->wp_ver ?? 'n/a' }}</strong></p>
                     </div>
                     
                  </div>
               </div>
            </div>
         </div>
    </div> --}}

    {{-- @include('components/stats/sharedborders') --}}

    {{-- <div class="w-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="px-6 py-4 border-b">
            <h2 class="text-2xl">
                Summary
            </h2>
            <p class="text-sm text-gray-500">
                Overview of some important site metrics.
            </p>
        </div>
        <div class="px-6 py-4">
            <div class="flex justify-between mb-4">
                <h6 class="font-medium">53 GB of 65 GB used</h6>
                <h6 class="font-medium">82% used</h6>
            </div>
            <div class="flex space-x-1.5 h-5 mb-7 rounded-full overflow-hidden">
                <div data-original-title="null" class=" has-tooltip"
                    style="background-color: rgb(255, 117, 87); width: 60.07%;"></div>
                <div data-original-title="null" class=" has-tooltip"
                    style="background-color: rgb(128, 225, 217); width: 4.13%;"></div>
                <div data-original-title="null" class=" has-tooltip"
                    style="background-color: rgb(159, 225, 128); width: 4.92%;"></div>
                <div data-original-title="null" class=" has-tooltip"
                    style="background-color: rgb(248, 188, 59); width: 0%;"></div>
                <div data-original-title="null" class=" has-tooltip"
                    style="background-color: rgb(150, 189, 255); width: 12.33%;"></div>
                <div data-original-title="null" class=" has-tooltip"
                    style="background-color: rgb(236, 239, 244); width: 18.55%;"></div>
            </div>
            <ul class="grid md:grid-cols-2 gap-y-3 gap-x-8">
                <li class="flex items-center">
                    <div class="rounded-full mr-3 w-7 h-2" style="background-color: rgb(255, 117, 87);"></div><span>Web
                        App</span><span class="ml-auto">39.0 GB (60.07%)</span>
                </li>
                <li class="flex items-center">
                    <div class="rounded-full mr-3 w-7 h-2" style="background-color: rgb(128, 225, 217);"></div>
                    <span>Database</span><span class="ml-auto">2.7 GB (4.13%)</span>
                </li>
                <li class="flex items-center">
                    <div class="rounded-full mr-3 w-7 h-2" style="background-color: rgb(159, 225, 128);"></div>
                    <span>Log</span><span class="ml-auto">3.2 GB (4.92%)</span>
                </li>
                <li class="flex items-center">
                    <div class="rounded-full mr-3 w-7 h-2" style="background-color: rgb(248, 188, 59);"></div>
                    <span>Tmp</span><span class="ml-auto">74.9 kB (0.00%)</span>
                </li>
                <li class="flex items-center">
                    <div class="rounded-full mr-3 w-7 h-2" style="background-color: rgb(150, 189, 255);"></div>
                    <span>Others</span><span class="ml-auto">8.0 GB (12.33%)</span>
                </li>
                <li class="flex items-center">
                    <div class="rounded-full mr-3 w-7 h-2" style="background-color: rgb(236, 239, 244);"></div>
                    <span>Free</span><span class="ml-auto">12 GB (18.55%)</span>
                </li>
            </ul>
        </div>
    </div> --}}

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="w-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-4 border-b">
                <h2 class="text-2xl">
                    Site Essentials
                </h2>
                <p class="text-sm text-gray-500">
                    Core metrics and configuration details
                </p>
            </div>
            <div>
                @each('site.listing.simple', $fields, 'field')
            </div>
        </div>

        <div class="w-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-4 border-b">
                <h2 class="text-2xl">
                    Extended Details
                </h2>
                <p class="text-sm text-gray-500">
                    Domain, client, and server information
                </p>
            </div>
            <div>
                @each('site.listing.simple', $additional_fields, 'field')
            </div>
        </div>
    </div>

    <div class="w-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="px-6 py-4 border-b">
            <h2 class="text-2xl">
                Database Structure
            </h2>
            <p class="text-sm text-gray-500">
                Storage allocation across database tables
            </p>
        </div>
        <div>
            @if ($this->getRecord()->sitemeta->db_tables)
                @foreach (json_decode($this->getRecord()->sitemeta->db_tables) as $table)
                    @include('site.listing.simple', [
                        'key' => $table->Name,
                        'field' => $table->Size == '0 MB' ? '< 1 MB' : $table->Size,
                    ])
                @endforeach
            @else
                <div class="px-6 py-4 border-b">
                    <h3>No database tables synced.</h3>
                </div>
            @endif

        </div>
    </div>




@endsection
