<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;

class PageSpeedInsightsService {

	public static function fetchInsights( $url, $strategy = 'mobile' ) {
		$apiKey = env( 'GOOGLE_API_PAGESPEED' );
		$apiUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

		// Specify the categories you want to include in the API request
		// https://developers.google.com/speed/docs/insights/rest/v5/pagespeedapi/runpagespeed#Category
		$categories = array( 'PERFORMANCE', 'ACCESSIBILITY', 'BEST_PRACTICES', 'SEO' );

		$response = Http::timeout( 200 )->get(
			$apiUrl,
			array(
				'url'      => $url,
				'strategy' => $strategy,
				'category' => $categories,
				'key'      => $apiKey,
			)
		);

		if ( $response->failed() ) {
			throw new \Exception( 'Google PageSpeed Insights API request failed.' );
		}

		return self::parseResponse( $response->json() );
	}

	public static function parseResponse( $data ) 
    {
		
        $metrics = array();

		if ( isset( $data['lighthouseResult'] ) ) 
        {
      
			$metrics = array(
				'performance'          => $data['lighthouseResult']['categories']['performance']['score'] ?? null, //
				'fcp'                  => $data['lighthouseResult']['audits']['first-contentful-paint']['numericValue'] ?? null, // millisecond
				'total_blocking_time'  => $data['lighthouseResult']['audits']['total-blocking-time']['numericValue'] ?? null, // millisecond
				'speed_index'          => $data['lighthouseResult']['audits']['speed-index']['numericValue'] ?? null, // miliseconds
				'lcp'                  => $data['lighthouseResult']['audits']['largest-contentful-paint']['numericValue'] ?? null, // millisecond
				// 'input_latency'        => $data['lighthouseResult']['audits']['estimated-input-latency']['numericValue'] ?? null,
				'time_interactive'     => $data['lighthouseResult']['audits']['interactive']['numericValue'] ?? null, // millisecond
				'fmp'                  => $data['lighthouseResult']['audits']['first-meaningful-paint']['numericValue'] ?? null, // sec
				'dom_size'              => $data['lighthouseResult']['audits']['dom-size']['numericValue'] ?? null, // elements
                'network_server_latency' => $data['lighthouseResult']['audits']['network-server-latency']['numericValue'] ?? null, //millisecond
                'server_response_time'   => $data['lighthouseResult']['audits']['server-response-time']['numericValue'] ?? null, // miliseconds

			);
		}

       

		// Optionally convert the scores from a 0-1 scale to a 0-100 scale
		foreach ( $metrics as $key => $value ) {
			// Convert to seconds
			if ( $key == 'performance' ) {
				$metrics[ $key ] = $value * 100;
			}

            if ( $key != 'dom_size' && $key != 'performance' && $value ) {
				$metrics[ $key ] = floor( $value );
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
