
<div class="flex items-center">   
    <figure class="h-8 w-8 overflow-hidden object-cover rounded-full bg-gray-100 flex items-center justify-center">
        <x-filament::avatar
        src="https://ps.w.org/{{ $getRecord()->name }}/assets/icon-128x128.png"
        onerror="this.onerror=null; this.src='https://ps.w.org/{{ $getRecord()->name }}/assets/icon-256x256.png'"
        alt="{{ $getRecord()->title }}"
        :circular="false"
        size="w-10 h-10"
        />
    </figure>
</div>


