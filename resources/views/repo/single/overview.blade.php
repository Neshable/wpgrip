@extends('repo/single/pagetemplate')

@section('content')

    @livewire('list-repo-sites', ['repo_model' => $this->getRecord() ] )

    @livewire('list-deployments', ['repository_id' => $this->getRecord()->id ])
    
@endsection
