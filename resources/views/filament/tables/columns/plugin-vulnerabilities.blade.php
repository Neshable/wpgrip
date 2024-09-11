    @php
        $vulnerabilities = $getState();
        $vulnerabilities = json_decode( $vulnerabilities );

    @endphp

    @if ( $vulnerabilities && !empty( $vulnerabilities) )
    @php
        $formatted_vulnerabilities = $this->check_vulnerability_database($vulnerabilities, $getRecord()->version );
    @endphp
        @if ( $formatted_vulnerabilities )
        <div>
            <h2>{{ count($formatted_vulnerabilities) }} vulnerabilities found</h2>

        
            @foreach ( $formatted_vulnerabilities as $vulnerabily )
            
            <div class="">
                <h3 class="fi-no-notification-title text-sm font-medium text-gray-950 dark:text-white">
                    {{ $vulnerabily['name'] }}
                </h3>
            
                <p class="fi-no-notification-body text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $vulnerabily['description'] }}
                </p>
            </div>

            @endforeach
        </div>
        @endif
    @endif