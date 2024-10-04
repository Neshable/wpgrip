


@extends('site/single/pagetemplate')

@section('content')    

@include('site.single.menus.security-page-submenu')

   {{-- @livewire('list-blacklist-monitors', ['site_id' => $this->getRecord()->id ]) --}}

@endsection
