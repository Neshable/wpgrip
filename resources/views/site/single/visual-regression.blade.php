

@if ( $getRecord()->sitemeta->home_shot )

<div class="grid grid-cols-2 gap-4">
    <div class="relative rounded-xl overflow-auto p-8">
      <h4>Screenshot For Reference</h4>
      <div class="text-center rounded-lg overflow-hidden w-56 sm:w-96 mx-auto">
        <img class="object-cover h-48 w-full " src="{{ Storage::url( $getRecord()->sitemeta->home_shot) }}">
      </div>
    </div>
    <!-- ... -->
    <div class="relative rounded-xl overflow-auto p-8">
      <h4>Challenger</h4>
      <div class="text-center rounded-lg overflow-hidden w-56 sm:w-96 mx-auto">
        <img class="object-cover h-48 w-full " src="{{ Storage::url( $getRecord()->sitemeta->home_shot_b) }}">
      </div>
    </div>
  </div>

@endif


