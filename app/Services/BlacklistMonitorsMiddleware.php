<?php

namespace App\Services;

use App\Models\Site;
use App\Models\BlacklistMonitor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use Filament\Notifications\Notification;

class BlacklistMonitorsMiddleware {

    /**
     * Site model or id
     *
     * @var Site|int
     */
    protected $site;

    /**
     * API key from the blacklist monitor
     *
     * @var string
     */
    protected $apiKey;

    public function __construct($site)
    {
        $this->site = $site instanceof Site ? $site : Site::findOrFail($site);
        $this->apiKey = env('BLACKLIST_API_KEY');

        if ($this->site && $this->apiKey) {
            $this->runCheck();
        }
    }

    public function runCheck()
    {
        // Check if the URL exists before making the request
        if (isset($this->site->url) && !empty($this->site->url)) {
            $parsedUrl = parse_url($this->site->url, PHP_URL_HOST);
            $domain = $parsedUrl ?: $this->site->url; // Use

            $response = Http::withBasicAuth($this->apiKey, '')
                            ->get('https://api.blacklistchecker.com/check/'.  $domain);
            
            if ($response->successful()) {
                $data = $response->json();
                $this->saveCheckResult($data);
            } else {
                // Handle error or log it
                $error = $response->json();
                Log::error('Blacklist check failed', $error);
            }
        } else {
            // Handle the case when the url does not exist
            Log::error('URL not found for blacklist check');
        }
    }
    
    /**
     * Save entry in the database
     *
     * @param array $data
     * @return void
     */
    protected function saveCheckResult(array $data)
    {
        BlacklistMonitor::create([
            'site_id' => $this->site->id,
            'status' => $data['status'],
            'input_raw' => $data['input_raw'],
            'input_type' => $data['input_type'],
            'ip_address' => $data['ip_address'],
            'detections' => $data['detections'],
            'blacklists' => json_encode($data['blacklists']),
            'checks_remaining' => $data['checks_remaining'],
        ]);
    }

  

}