<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;

class PageSpeedInsightsService
{
    public static function fetchInsights($url, $strategy = 'mobile')
    {
        $apiKey = env('GOOGLE_API_PAGESPEED');
        $apiUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed";
        
        // Specify the categories you want to include in the API request
        // https://developers.google.com/speed/docs/insights/rest/v5/pagespeedapi/runpagespeed#Category
        $categories = [ 'PERFORMANCE', 'ACCESSIBILITY', 'BEST_PRACTICES', 'SEO' ];

        $response = Http::timeout(200)->get($apiUrl, [
            'url' => $url,
            'strategy' => $strategy,
            'category' => $categories,
            'key' => $apiKey
        ]);

        if ( $response->failed() ) {
            throw new \Exception('Google PageSpeed Insights API request failed.');
        }

        return self::parseResponse($response->json());
    }

    public static function parseResponse($data)
    {
        $lighthouse = $data['lighthouseResult'];
        
        if ( $lighthouse )
        {
            $metrics = [
                'performance' => $lighthouse['categories']['performance']['score'] ?? null,
                'fcp' => $lighthouse['audits']['first-contentful-paint']['numericValue'] ?? null,// sec
                'total_blocking_time' => $lighthouse['audits']['total-blocking-time']['numericValue'] ?? null, //sec
                'speed_index' => $lighthouse['audits']['speed-index']['numericValue'] ?? null, // sec
                'lcp' => $lighthouse['audits']['largest-contentful-paint']['numericValue'] ?? null,// sec
                'time_interactive' => $lighthouse['audits']['interactive']['numericValue'] ?? null, // sec
                'fmp' => $lighthouse['audits']['first-meaningful-paint']['numericValue'] ?? null,// sec
                'server_response_time' => $lighthouse['audits']['server-response-time']['numericValue'] ?? null, // miliseconds
                
            ];
        }
       
   

        // Optionally convert the scores from a 0-1 scale to a 0-100 scale
        foreach ($metrics as $key => $value) {
            // Convert to seconds
            if ( $key == 'fcp' || $key == 'lcp' || $key == 'fmp' || $key == 'total_blocking_time' || $key == 'time_interactive' || $key == 'speed_index'  ) {
                $metrics[$key] = (float) number_format( $value / 1000, 1, '.', '');
            }

            if ( $key == 'performance' ) {
                $metrics[$key] = $value * 100;
            }

        }

        // "performance" => 22.0
        // "fcp" => 6.0
        // "total_blocking_time" => 0.6
        // "speed_index" => 13.2
        // "lcp" => 17.7
        // "time_interactive" => 1844425.0 //milliseconds
        // "fmp" => 598650.0
        // "server-response-time" => 43200


        return $metrics;
    }
}
