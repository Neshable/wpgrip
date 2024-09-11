<?php

namespace App\Jobs\Staging;

use App\Models\Site;
use App\Models\StagingSite;
use App\Models\User;
use App\Models\Backup;
use App\Services\SSHSiteConnect;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TestConnection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The StagingSite instance.
     *
     * @var \App\Models\StagingSite
     */
    public $staging_site;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( StagingSite $site )
    {
        $this->staging_site = $site;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->staging_site ) {

            // Init a new connection to websites's server.
            $connection = new SSHSiteConnect( $this->staging_site );

            // If we don't have connection abort and send notification.
            if ( !$connection->active ) 
            {
                Notification::make()
                    ->title('Login failed: Unauthorized.')
                    ->danger()
                    ->duration(5000)
                    ->send();

                $this->staging_site->status = 'inactive';
                $this->staging_site->save();

                return false;
            }

            $this->staging_site->status = 'connected';
            try{
                $what = $this->staging_site->save();
            }
            catch (\PDOException $e) {
                echo $e->getMessage();
            }
            


            Notification::make()
                ->title('Connection successful!')
                ->success()
                ->duration(5000)
                ->send();

        
            $connection->close();
            
            return true;
        }   
    }
}
