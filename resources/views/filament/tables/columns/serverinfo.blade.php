<div class="w-full py-4">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold mb-2">{{ $getRecord()->name }}</h2>
        <span class="bg-green-200 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Connected</span>
    </div>
  
    <div class="border-t border-gray-200 my-2"></div>
    <p class="text-gray-500 mb-6">{{ $getRecord()->ip }}</p>

    <div class="grid gap-y-1 py-1">
      
        <span class="text-gray-500">Hosting type: {{ $getRecord()->type }}</span>
        <span class="text-gray-500">SSH Port: {{ $getRecord()->ssh_port }}</span>
    </div>

  
    <div class="border-t border-gray-200 my-2"></div>
    <div class="flex items-center">
        <div class="flex inline-flex items-center gap-2 cursor-pointer">
            <div class="">
                <x-filament::icon
                x-tooltip="{
                    content: 'Total websites on this server',
                    theme: $store.theme,
                }"
                icon="heroicon-o-globe-alt"
                class="h-5 w-5"
                />
            </div>
            <span class=""> {{ $getRecord()->sites()->count() }} sites total</span>
        </div>
    </div>
</div>

