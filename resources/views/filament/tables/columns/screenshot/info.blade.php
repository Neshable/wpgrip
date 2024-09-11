
<x-filament::modal width="5xl">
    <x-slot name="trigger">
        <div class="w-full">
            <div class="relative justify-center flex items-end overflow-hidden rounded-xl">
        

                <img src="{{ $getRecord()->getImageURL() }}" lazy="on-load" alt="vrt">
                <div class="absolute inset-0 bg-black opacity-10"></div>
                
                {{-- <div class="absolute flex justify-center bottom-0 mb-3">
                    <div class="flex bg-white px-4 py-1 space-x-5 rounded-lg overflow-hidden shadow">
                        
                            <div class="flex inline-flex items-center gap-2 cursor-pointer">
                                <div class="">
                                    <x-filament::icon
                                    x-tooltip="{
                                        content: 'Screenshot taken on {{ $getRecord()->created_at }}',
                                        theme: $store.theme,
                                    }"
                                    icon="heroicon-o-calendar-days"
                                    class="h-5 w-5"
                                    />
                                </div>
                                <span class="">{{ $getRecord()->created_at }}</span>
                            </div>
                        
                    </div>
                </div> --}}
        
                <span class="absolute top-0 left-0 inline-flex mt-3 ml-3 px-3 py-2 rounded-lg z-10 bg-green-200 text-green-800 text-xs font-semibold">{{ $getRecord()->similarity }}/100</span>
        
                </div>
            
                
            <div class="grid grid-cols-2 mt-8">
                <div class="flex items-center">
                <div class="relative">
                    <div class="">
                        <x-filament::icon
                        x-tooltip="{
                            content: 'Screenshot taken on {{ $getRecord()->created_at }}',
                            theme: $store.theme,
                        }"
                        icon="heroicon-o-calendar-days"
                        class="h-5 w-5"
                        />
                    </div>
                </div>
        
                </div>
        
                <div class="flex justify-end">
                <p class="inline-block text-primary whitespace-nowrap">
                    {{ $getRecord()->created_at }}
                </p>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="heading">
        Compare the screenshots
    </x-slot>

    <x-slot name="description">
        We detected a similarity score of {{ $getRecord()->similarity }} out of 100
    </x-slot>

    <x-img-comparison-slider 
    :firstImage="Storage::disk('public')->url($getRecord()->file_path)" 
    :secondImage="Storage::disk('public')->url($this->getSiteModel()->screenshot_path)" />

</x-filament::modal>
  

