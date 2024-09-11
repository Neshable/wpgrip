@extends('site/single/pagetemplate')

@section('content')

    @include('site/single/headertemplate', [ 
        'title' => 'Core',
        'icon' => 'icon-wordpress' ])

    @livewire('get-wordpress-info', [ 'site' => $this->getRecord() ])
    
@endsection
