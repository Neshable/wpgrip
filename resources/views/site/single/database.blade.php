@extends('site/single/pagetemplate')

@section('content')

    @include('site/single/headertemplate', [ 
        'title' => 'Database information',
        'icon' => 'heroicon-m-circle-stack' ])

    @include('site/notifications/general')


    <div class="w-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="px-6 py-4 border-b">
            <h2 class="text-2xl">
                Database tables
            </h2>
            <p class="text-sm text-gray-500">
                List of available tables in the current site's database.
            </p>
        </div>
        <div>
            @if ( $this->getRecord()->sitemeta->db_tables )
                @foreach ( json_decode( $this->getRecord()->sitemeta->db_tables ) as $table )
                    @include('site.listing.simple', [
                        'key' => $table->Name, 
                        'field' => $table->Size == '0 MB' ? '< 1 MB' : $table->Size
                    ])
                @endforeach
            @else
              <div class="px-6 py-4 border-b">
                <h3>No database tables synced.</h3>
            </div>
              
            @endif
    
        </div>
    </div>

    
@endsection

