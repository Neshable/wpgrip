@extends('site/single/pagetemplate')


@section('content')
    @include('site.single.menus.tools-page-submenu')

    <x-filament::section>

        <x-slot name="heading">
            All Backup snapshots
        </x-slot>

        {{-- <x-slot name="description">
            Full list of your available backups
        </x-slot> --}}


            <p class="mb-4">
                We highly recommend conduction backups directly from your server or hosting. This method is typically more
                efficient, cost-effective, and quicker as it doesn't require data transfer over a network.
            </p>
            <p class="mb-4">
                Remember to regularly backup not just your data, but also WordPress settings and server configurations,
                and store them in a separate, secure location. This ensures quick restoration and recovery during unexpected
                events, maintaining your operations' continuity.
            </p>


    </x-filament::section>


    @if (!$this->getRecord()->ssh_connection)
        @include('site/notifications/general')
    @else
        @livewire('list-backups', ['site_id' => $this->getRecord()->id])
    @endif
@endsection
