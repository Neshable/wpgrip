<?php

namespace App\Jobs;


use App\Models\Site;
use App\Models\User;

use Spatie\SslCertificate\SslCertificate;
use Illuminate\Support\Facades\Storage;

use Filament\Notifications\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckSSLExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

   /**
     * Create a new job instance.
     *
     * @param  \App\Models\Site  $site
     * @return void
     */
    public function __construct(Site $site)
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
        if ( $this->site->url )
        {
            // fetch the certificate using an url
            $certificate = SslCertificate::createForHostName( $this->site->url );

            if ( $certificate && $certificate->isValid() ) {
                // Update the model.
                $this->site->ssl_expiry_date = $certificate->expirationDate();
                $this->site->is_ssl_active = 1;
                $this->site->save();

                // Save to DB and push notification.
                Notification::make()
                    ->title('Certificate is valid.')
                    ->success()
                    ->body('Certificate is issued from ' . $certificate->getIssuer() . ' and expiries in ' . $certificate->expirationDate()->diffInDays() . ' days') 
                    ->send();
            }

            // $certificateProperties = $certificate->toArray();
            // $certificate->getIssuer(); // returns "Let's Encrypt Authority X3"
            // $certificate->isValid(); // returns true if the certificate is currently valid
            // $certificate->validFromDate(); // returns a Carbon instance Carbon
            // $certificate->expirationDate(); // returns a Carbon instance Carbon
            // $certificate->lifespanInDays(); // return the amount of days between  validFromDate and expirationDate
            // $certificate->expirationDate()->diffInDays(); // returns an int
            // $certificate->getSignatureAlgorithm(); // returns a string
            // $certificate->getOrganization(); // returns the organization name when available
            // $certificate->getFingerprintSha256();
           
        }
        
    }
}
