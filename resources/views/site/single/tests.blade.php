@extends('site/single/pagetemplate')

@section('content')
    
@php
    $tenant = Filament\Facades\Filament::getTenant(); 
@endphp

    <div>
    @include('site/single/headertemplate', [ 
            'title' => 'Tests',
            'icon' => 'heroicon-m-computer-desktop' ])

    </div>

    <x-filament::section> 
        
        <x-slot name="heading">
            Visual Regression Tests 
        </x-slot>

        <x-slot name="headerEnd">
          
            <x-filament::button wire:click="mountAction('generateHome')" outlined>
                Re-generate control homescreen
            </x-filament::button>

             <script>
                window.livewire.on('homeGenerated', () => {
                    location.reload();
                })
            </script>

            <x-filament::button wire:click="runVRTTest" outlined>
                Run VRT Test
            </x-filament::button>

        </x-slot> 
   
        <div class="flex flex-col items-center bg-white dark:bg-gray-900 dark:text-white border-gray-200 rounded-lg  md:flex-row">
            @if ( $this->getRecord()->screenshot_path && Storage::disk('public')->exists($this->getRecord()->screenshot_path))
                <img class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-96 md:rounded-none md:rounded-l-lg" 
                src="{{ Storage::disk('public')->url($this->getRecord()->screenshot_path) }}" alt="">
            @endif 
           
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Homepage Control</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                    VRTs – Visual Regression Tests is a tool to test your website for unwanted visual changes. The tool creates daily comparison screenshots ( or by demand ) and compares them with a reference snapshot. If there is a difference between the screenshots, you'll be notified. Use three comparison modes to spot the differences easily.
                </p>
            </div>
           

        </div>

        
        

    </x-filament::section>

    {{-- <div
    x-data="{}"
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('img-comparison-css'))]"
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('img-comparison-js'))]"
    >
    @livewire('list-screenshots', ['site_id' => $this->getRecord()->id ] )   
    </div>  --}}
@endsection
