@extends('repo/single/pagetemplate')

@section('content')

    {{-- @include('site/notifications/general') --}}
    
    <div class="grid grid-cols-12 gap-8">
        
        <div class="col-span-12 sm:col-span-12 md:col-span-12 flex flex-col gap-y-8">
            <x-filament::section>
                <x-slot name="heading">
                   Webhook deploy
                </x-slot>
        
                <x-slot name="headerEnd">
                    <x-filament::link
                    icon="heroicon-m-clipboard" 
                    href="#"
                    color="info"
                    x-on:click="
                    window.navigator.clipboard.writeText('https://app.wpgrip.com/webhook/git/{{ $this->getRecord()->webhook }}')
                    $tooltip('Copied webhook', { timeout: 2000 })" >
                        Copy webhook
                    </x-filament::link>
                </x-slot>

        
                Add this URL to your git webhooks to enable automatic deployments when you push your changes to your current branch. However if you need more control, you can look at Deployment Script section.
                
                <x-filament::input.wrapper class="mt-3" disabled>
                    <x-filament::input
                        type="text"
                        label="asd"
                        wire:model="name"
                        value="https://app.wpgrip.com/webhook/git/{{ $this->getRecord()->webhook }}"
                        disabled
                    />
                </x-filament::input.wrapper>

                @if ( !$this->getRecord()->secret )
                <x-filament::badge size="sm" color="warning">
                    You need to add the secret key in your repository settings if you want to use the webhook URL.
                </x-filament::badge>
                @endif
        
               
            </x-filament::section>
        </div>
    </div>
    
    @livewire('list-repo-sites', ['repo_model' => $this->getRecord() ] )

    @livewire('list-commits', ['repository_id' => $this->getRecord()->id ])
    
@endsection

