<x-button-link.default
    {{ $attributes }}
    {{ $attributes->merge(['class' => 'text-white rounded-md py-3 px-5 inline-flex bg-blue-600 hover:bg-blue-800 focus:ring-secondary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800'])}}
>
    {{ $slot }}
</x-button-link.default>

