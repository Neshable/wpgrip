
@php
 

@endphp

@extends('site/single/pagetemplate')

@section('content')    
    
    @include('site.single.menus.monitor-page-menu')
    @livewire('list-monitors', ['site_id' => $this->getRecord()->id ])

@endsection
