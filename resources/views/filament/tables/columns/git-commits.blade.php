<x-filament::modal>
    <x-slot name="trigger">
        <x-filament::link tag="button">
            Latest 5 commits        
        </x-filament::link>
    </x-slot>
 

    <x-slot name="heading">
        Git history
    </x-slot>
 
    <x-slot name="description">
        The last 5 commits on record.
    </x-slot>
 
    @php
        $commits = $getState();
    @endphp
    @if ( $commits )
    @foreach ($commits as $commit )
    <div class="mt-0.5 grid">
        <h3 class="fi-no-notification-title text-sm font-medium text-gray-950 dark:text-white">
            {{ $commit['subject'] }}
        </h3>
        <time class="fi-no-notification-date text-sm text-gray-500 dark:text-gray-400 mt-1">
            Commit #{{ $commit['commit'] }}
        </time>
        <x-filament::link
            wire:click="openNewUserModal"
            tag="button"
        >
            Revert back to this commit
        </x-filament::link>
        <p class="fi-no-notification-body text-sm text-gray-500 dark:text-gray-400 mt-1">
            Pushed by  #{{ $commit['author']['name'] }}
        </p>
    </div>

    @endforeach
    @endif
</x-filament::modal>

