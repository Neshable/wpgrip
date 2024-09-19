

<div style="min-width: 150px;" class="whitespace-normal">
    <div class="gap-x-2 px-3 py-4 inline-flex items-center">     
        <div class="text-md font-bold text-gray-500">
            {{ $getRecord()->version }} 
        </div>

        @if ( $getRecord()->is_vulnerable )
        
        <x-filament::modal  width="4xl">
            <x-slot name="trigger">
                <x-filament::icon-button
                    icon="heroicon-m-shield-exclamation"
                    color="danger"
                    label="Vulnerabilities found"
                />
            </x-slot>
        
            <x-slot name="heading">
                Some vulnerabilities found in this version
            </x-slot>

            <x-slot name="description">
                We detected that the current version ({{ $getRecord()->version }} ) of the plugin has some vulnerabilities. An update is strongly recommended.
            </x-slot>
        
            @php
                $vulnerabilities = $getRecord()->vuln_ids;
                $vulnerabilities = json_decode($vulnerabilities, true);

                $all_vulnerabilities = App\Models\Vulnerability::find($vulnerabilities);
            @endphp
        
            @if ( $all_vulnerabilities )
  
                <strong>{{ count($all_vulnerabilities) }} vulnerabilities found</strong>

                @foreach ($all_vulnerabilities as $single)
                    <div>
                        <div class="mb-4 flex items-center p-5 leading-normal text-red-600 bg-red-100 rounded-lg" role="alert">
                            {{ $single->name }}
                        </div>
            
                        {{-- Decode the JSON impact field into a PHP array --}}
                        @php
                            $impact = json_decode($single->impact, true);
                        @endphp
            
                        {{-- Check if impact is not an empty array --}}
                        @if (!empty($impact))
                            {{-- Check if CVSS information is available --}}
                            @if (isset($impact['cvss']))
                                <div class="mb-4">
                                    <h4>CVSS Information:</h4>
                                    <ul>
                                        <li><strong>Version:</strong> {{ $impact['cvss']['version'] ?? 'N/A' }}</li>
                                        <li><strong>Score:</strong> {{ $impact['cvss']['score'] ?? 'N/A' }}</li>
                                        <li><strong>Severity:</strong> {{ $impact['cvss']['severity'] ?? 'N/A' }}</li>
                                    </ul>
                                </div>      
                            @endif
            
                            {{-- Check if CWE information is available --}}
                            @if (isset($impact['cwe']) && is_array($impact['cwe']))
                                @foreach ($impact['cwe'] as $cwe)
                                    <div class="mb-4">
                                        <h4>CWE Information:</h4>
                                        <p><strong>CWE ID:</strong> {{ $cwe['cwe'] ?? 'N/A' }}</p>
                                        <p><strong>Name:</strong> {{ $cwe['name'] ?? 'N/A' }}</p>
                                        <p><strong>Description:</strong> {{ $cwe['description'] ?? 'N/A' }}</p>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            <p>No detailed impact information available.</p>
                        @endif
                    </div>
                @endforeach
            @endif
            {{-- <x-slot name="footer">
                Source
            </x-slot> --}}
        </x-filament::modal>
       
        @endif
        
    </div>

</div>