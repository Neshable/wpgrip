<div>
    

@if ($loading)
    Loading...
@else

    @if ($data && isset($data['offers']))
    
    <x-filament::section icon="wordpress" icon-color="{{ version_compare($this->site->wp_ver, $data['offers'][0]['current'], '<') ? 'warning' : 'success' }}">    
        <x-slot name="heading">
            @if (version_compare($this->site->wp_ver, $data['offers'][0]['current'], '<'))
                Your WordPress version is outdated ({{ $this->site->wp_ver }})
            @else
                Your WordPress version is up to date. ({{ $this->site->wp_ver }})
            @endif
        </x-slot>

        <x-slot name="headerEnd">
            @if ( $this->site->status && $this->site->status == \App\Enums\SiteStatus::UpdatingCore )
                Updating
                <x-filament::loading-indicator class="h-5 w-5" />
                <script>
                    setTimeout(function(){
                       location.reload();
                    }, 8000);
                </script>
            @else
                {{ ($this->updateAction)(['site_id' => $this->site->id ]) }}
            @endif
            <x-filament-actions::group
                :actions="[
                    ($this->downgradeAction)(['site_id' => $this->site->id ]),
                ]"
                label="Actions"
                icon="heroicon-m-ellipsis-vertical"
                color="primary"
                size="md"
                tooltip="More actions"
                dropdown-placement="bottom-start"
            />

            <x-filament-actions::modals />
        </x-slot>

        <div>
            @foreach ($data['offers'] as $offer)
                Download Link: <a href="{{ $offer['download'] }}">{{ $offer['download'] }}</a><br/>
                Locale: {{ $offer['locale'] }}<br/>
                Current Version: {{ $offer['current'] }}<br/>
                Min PHP Version: {{ $offer['php_version'] }}<br/>
                Min MySQL Version: {{ $offer['mysql_version'] }}<br/>
            @endforeach
    
        </div>
        
    </x-filament::section>      
    @endif
@endif
</div>
