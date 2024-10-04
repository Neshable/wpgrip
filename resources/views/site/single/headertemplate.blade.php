<header class="flex flex-col gap-3 overflow-hidden sm:flex-row sm:items-center pl-0">
    <x-filament::icon
    icon="{{ $icon ?? 'icon-wordpress' }}"
    class="h-7 w-7 transition duration-75 text-gray-400 dark:text-gray-500"
    />
    <div class="grid flex-1 gap-y-1">
        <h2 class="font-semibold text-2xl text-gray-950 dark:text-white">
        {{ $title ?? 'Page' }}
        </h2>
    </div>
</header>



