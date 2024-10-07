<?php

namespace App\Jobs\Domain;

use Iodev\Whois\Factory as WhoisFactory;
use Exception;
use Carbon\Carbon;
use App\Models\Site;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\GripNotifications;

class SingleDomainExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $site;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site )
    {
        $this->site = $site;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Extract the domain name from the URL
            $parsedUrl = parse_url($this->site->url, PHP_URL_HOST);
            if (!$parsedUrl) {
                throw new Exception("Invalid URL format.");
            }

            // Instantiate the Whois object
            $whois = WhoisFactory::get()->createWhois();
            
            // Perform Whois Lookup
            $info = $whois->loadDomainInfo($parsedUrl);

            if ($info && $info->expirationDate) {
                // Update the expiry_date in the Site model with only the date part
                $expiryDate = Carbon::createFromTimestamp($info->expirationDate)->toDateString(); // YYYY-MM-DD format
                $this->site->sitemeta->domain_expiry_date = $expiryDate;
                $this->site->sitemeta->save();

                // Now send notifications if there is little time.
                $now = Carbon::now();  
                $diffInDays = $now->diffInDays($expiryDate, false);

                // In expires in 1 month or less.
                if ($diffInDays <= 30 && $diffInDays > 7) {
                    // GripNotifications::getCustomWarningToDB( 
                    //     'Your domain expires in less than 1 month', 
                    //     'P',
                    // );
                }
                // Expires less than a week.
                if ($diffInDays <= 7) {
                    // GripNotifications::sendOneWeekNotice($this->site);
                }

            } else {
                throw new Exception("Domain expiration date could not be retrieved.");
            }
        } catch (Exception $e) {
            // Log an error or handle it according to your app logic
            \Log::error('Error fetching domain expiry date: ' . $e->getMessage());
        }
    }
  
}
