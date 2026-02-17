@extends('site/single/pagetemplate')

@section('content')

{{-- Section 1: Cache Management --}}
<x-filament::section>

    <x-slot name="heading">
        Cache Management
    </x-slot>

    <x-slot name="description">
        Clear the cache from your installed caching plugin.
    </x-slot>

    <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4">
        <div class="md:grid md:grid-rows-2 items-center space-y-1">
            <div class="font-semibold leading-6 text-gray-950 dark:text-white">
                Clear plugin cache
            </div>
            <div class="text-gray-500">
                Select your caching plugin and clear its cache via WP-CLI.
            </div>
        </div>
        <div class="text-right">
            <x-filament::button wire:click="mountAction('clearOtherCache')" outlined>
                Clear cache
            </x-filament::button>
        </div>
    </div>

</x-filament::section>


{{-- Section 2: Predefined WP-CLI Tools --}}
<x-filament::section>

    <x-slot name="heading">
        WP-CLI Tools
    </x-slot>

    <x-slot name="description">
        Common WordPress maintenance commands. Output is displayed below after each run.
    </x-slot>

    <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="font-semibold leading-6 text-gray-950 dark:text-white">Verify core checksums</div>
            <div class="text-gray-500 text-sm">Verifies WordPress file integrity against the official checksums.</div>
        </div>
        <div class="text-right">
            <x-filament::button wire:click="runVerifyChecksums" outlined>
                Run
            </x-filament::button>
        </div>
    </div>

    <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="font-semibold leading-6 text-gray-950 dark:text-white">Check database</div>
            <div class="text-gray-500 text-sm">Checks all database tables for errors using mysqlcheck.</div>
        </div>
        <div class="text-right">
            <x-filament::button wire:click="runDbCheck" outlined>
                Run
            </x-filament::button>
        </div>
    </div>

    <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="font-semibold leading-6 text-gray-950 dark:text-white">List users</div>
            <div class="text-gray-500 text-sm">Lists all WordPress users with their roles.</div>
        </div>
        <div class="text-right">
            <x-filament::button wire:click="runUserList" outlined>
                Run
            </x-filament::button>
        </div>
    </div>

    <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4">
        <div>
            <div class="font-semibold leading-6 text-gray-950 dark:text-white">Flush rewrite rules</div>
            <div class="text-gray-500 text-sm">Flushes WordPress rewrite rules (hard flush).</div>
        </div>
        <div class="text-right">
            <x-filament::button wire:click="runRewriteFlush" outlined>
                Run
            </x-filament::button>
        </div>
    </div>

</x-filament::section>


{{-- Section 3: Custom WP-CLI Command --}}
<x-filament::section>

    <x-slot name="heading">
        Custom WP-CLI Command
    </x-slot>

    <x-slot name="description">
        Run any WP-CLI command. Only commands starting with "wp " are permitted. Shell operators are blocked for security.
    </x-slot>

    <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4">
        <div class="text-gray-500">
            Type a WP-CLI command (e.g. <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">wp option get siteurl</code>).
        </div>
        <div class="text-right">
            <x-filament::button wire:click="mountAction('runCustomCommand')" color="warning" outlined>
                Run custom command
            </x-filament::button>
        </div>
    </div>

</x-filament::section>


{{-- Section 4: Command Output (conditional) --}}
@if ( $commandOutput !== '' )
<x-filament::section>

    <x-slot name="heading">
        Command Output
    </x-slot>

    <x-slot name="description">
        {{ $lastCommandLabel }}
    </x-slot>

    <pre class="mt-2 text-xs font-mono bg-gray-950 text-green-400 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto whitespace-pre-wrap break-words">{{ $commandOutput }}</pre>

</x-filament::section>
@endif


{{-- Section 5: HTTP Status Check --}}
<x-filament::section>

    <x-slot name="heading">
        HTTP Status
    </x-slot>

    <div class="md:grid md:grid-cols-2 items-center md:space-y-0 space-y-1 py-4">
        <div class="text-gray-500">
            Check the HTTP status code of the main site URL.
        </div>
        <div class="text-right">
            <x-filament::button wire:click="checkStatusCode" outlined>
                Check Status Code
            </x-filament::button>
        </div>
    </div>

</x-filament::section>

<x-filament-actions::modals />

@endsection
