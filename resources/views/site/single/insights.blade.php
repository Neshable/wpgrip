@extends('site/single/pagetemplate')

@section('content')
@if ($shelleyPort > 0)
    <div style="height: calc(100vh - 140px); min-height: 600px; border-radius: 0.5rem; overflow: hidden; border: 1px solid #e5e7eb;">
        <iframe
            src="https://wpgrip.exe.xyz:{{ $shelleyPort }}/"
            style="width: 100%; height: 100%; border: none; display: block;"
            allow="clipboard-read; clipboard-write"
            title="Shelley AI Agent — {{ $record->name }}"
        ></iframe>
    </div>
@elseif ($shelleyError)
    <x-filament::section>
        <x-slot name="heading">AI Agent — Error</x-slot>
        <div class="text-red-500 font-mono text-sm p-4 bg-red-50 rounded">
            {{ $shelleyError }}
        </div>
    </x-filament::section>
@else
    <x-filament::section>
        <x-slot name="heading">AI Agent</x-slot>
        <div class="text-gray-500 p-4">Starting Shelley agent, please refresh...</div>
    </x-filament::section>
@endif
@endsection
