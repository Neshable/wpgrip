
@php
 

@endphp

@extends('site/single/pagetemplate')

@section('content')    
    
    @livewire('list-monitors', ['site_id' => $this->getRecord()->id ])

@endsection
