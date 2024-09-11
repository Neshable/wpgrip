@php
    $monitor = $getRecord()->monitor;
@endphp

@if (  $monitor )

<div>{{ $monitor->uptime_status }}</div>

<div>{{ $monitor->certificate_expiration_date }}</div>

<div>{{ $monitor->certificate_issuer }}</div>

@endif
