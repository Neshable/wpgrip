@extends('site/single/pagetemplate')

@section('content')

    @include('site.single.menus.tools-page-submenu')

     <x-filament::section> 
        
        <x-slot name="heading">
            Useful Tools
        </x-slot>

        <x-slot name="description">
            Additional WordPress tools to guarantee the seamless operation of your site.
        </x-slot>

        <x-slot name="headerEnd">
            
        </x-slot>   

        <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4">
            <div class="text-gray-600">
                Check the status code of the main URL.
            </div>
            <div class="text-right">
                <x-filament::button wire:click="checkStatusCode" outlined>
                    Check Status Code
                </x-filament::button>
            </div>
        </div>

        <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4">
            <div class="md:grid md:grid-rows-2 items-center space-y-1">
                <div class="font-semibold leading-6 text-gray-950 dark:text-white">
                    Clear WPRocket cache
                </div>
                <div class="text-gray-500">
                    Clears the cache by WP Rocket plugin using their <a href="https://github.com/GeekPress/wp-rocket-cli">official CLI package</a> and commands.
                </div>
            </div>
            
            <div class="text-right">
                <x-filament::button wire:click="mountAction('clearCache')" outlined>
                    Clear cache
                </x-filament::button>
            </div>
        </div>

        <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4">
            <div class="md:grid md:grid-rows-2 items-center space-y-1">
                <div class="font-semibold leading-6 text-gray-950 dark:text-white">
                    Clear other cache
                </div>
                <div class="text-gray-500">
                    Clears the object cache and the cache from other plugins.
                </div>
            </div>
            
            <div class="text-right">
                <x-filament::button wire:click="mountAction('clearOtherCache')" outlined>
                    Clear cache
                </x-filament::button>
            </div>
        </div>

        <x-filament-actions::modals />
    
        
    </x-filament::section>
@endsection
