@php
   $threshold = 10; // Set to db option.
   $percentage = false;
  if ( $getRecord()->hdd_total ) {
      $total_percentage = (($getRecord()->hdd_total - ( $getRecord()->hdd_total - $getRecord()->hdd_free ) ) / $getRecord()->hdd_total) *100;
      $percentage = (int) $total_percentage;
  }
@endphp

@if ( $percentage )

<div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10 shadow w-full " style="background-color: #CFD6E4">
    <div style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600); width: {{$percentage ?? $percentage}}%" class="rounded-xl bg-primary-500 text-xs leading-none py-1 text-center text-white">
    </div>
</div>
@endif

