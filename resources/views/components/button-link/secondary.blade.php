<x-button-link.default
    {{ $attributes->merge(['class' => 'rounded-md inline-flex text-sm font-medium transition-all ease-in-out duration-100 focus:outline-none focus:ring border rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:border-gray-400 focus:bg-white px-3 py-2 text-sm'])}}
>
    {{ $slot }}
</x-button-link.default>
