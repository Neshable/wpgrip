@php
    $tenant = Filament\Facades\Filament::getTenant(); 
    $products = App\Models\Product::all();
@endphp

@foreach($products as $product)
    @subscribed($product->slug, $tenant)
        <div class="mt-2 bg-blue-100 border border-blue-200 text-sm text-blue-800 rounded-lg p-4 dark:bg-blue-800/10 dark:border-blue-900 dark:text-blue-500" role="alert" tabindex="-1" aria-labelledby="hs-soft-color-info-label">
            <span id="hs-soft-color-info-label" class="font-bold">{{$product->name}}</span> plan.
        </div>
    @endsubscribed
@endforeach

