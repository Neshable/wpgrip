<div class="container mx-auto ">
    <div class="flex flex-wrap">
        <div class="w-full">
            <h2 class="text-2xl font-semibold mb-2">Blacklist Monitors</h2>
            <div class="bg-white overflow-hidden rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-gray-800 font-semibold">Blacklist Name</div>
                        <div class="text-gray-800 font-semibold text-center">Result</div>
                    </div>
                </div>
                @foreach(json_decode($listing, true) as $item)
                <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-gray-700">{{ $item['name'] }}</div>
                        <div class="text-center {{ $item['detected'] ? 'text-red-600' : 'text-green-600' }}">
                            {{ $item['detected'] ? 'FAIL' : 'PASS' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
