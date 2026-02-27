<li {{ $attributes->merge(['class' => 'flex items-center gap-2.5 text-sm text-neutral-300 font-light']) }}>
    <svg class="h-4 w-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <span>{{ $slot }}</span>
</li>
