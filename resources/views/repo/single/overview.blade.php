@extends('repo/single/pagetemplate')

@section('content')

    {{-- @include('site/notifications/general') --}}
    
    <div class="grid grid-cols-12 gap-8">
        
        <div class="col-span-12 sm:col-span-12 md:col-span-12 flex flex-col gap-y-8">
           
        </div>
    </div>
    
    @livewire('list-repo-sites', ['repo_model' => $this->getRecord() ] )

    @livewire('list-deployments', ['repository_id' => $this->getRecord()->id ])
    
@endsection

