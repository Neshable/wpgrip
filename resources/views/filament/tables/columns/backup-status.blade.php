@if ( $getRecord()->status == 'active' )
<x-filament::badge color="success">
    complete
</x-filament::badge> 
@else
<x-filament::loading-indicator x-tooltip="{
    content: 'Backup is in process',
    theme: $store.theme,
}" class="h-4 w-4" />
@endif