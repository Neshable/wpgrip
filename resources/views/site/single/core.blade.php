@extends('site/single/pagetemplate')

@section('content')

    @include('site.single.menus.updates-page-submenu')

    @livewire('get-wordpress-info', [ 'site' => $this->getRecord() ])
    
@endsection
