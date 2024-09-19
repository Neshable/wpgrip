<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Models\Plugin;
use App\Models\Vulnerability;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckPluginVulnerabilities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * Boolean if the plugin is vulnerable or not
     *
     * @var bool
     */
    public $is_vulnerable;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site )
    {
        // Get original production site.
        $this->site = $site;
        $this->is_vulnerable = false;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site ) {
            // Get all site plugins.
            $plugins = $this->site->plugins;
            // Loop through the plugin and compare with our vulnerability database.
            foreach( $plugins as $plugin ) {
                $vulnerabilities = Vulnerability::query()->where('type', 'plugin' )->where('slug', $plugin->name )->get();

                $this->is_vulnerable = false;
                $vuln_ids = array();

                // Check against the vulnerabilities.
                if ( $vulnerabilities ) {
                    foreach ( $vulnerabilities as $single_vulnerability ) {
                        if (isset( $single_vulnerability->max_version ) && isset( $plugin->pivot->version ) ) {
                            if ( version_compare($plugin->pivot->version, $single_vulnerability->max_version ) <= 0 ) {
                                $this->is_vulnerable = true;
                                // Push the IDs in the array for later use.
                                array_push($vuln_ids, $single_vulnerability->id );    
                            }
                        }
                    }                 
                }

                // Update the pivot boolean.
                $plugin->pivot->update( [
                    'is_vulnerable' => $this->is_vulnerable,
                    'vuln_ids'  => json_encode($vuln_ids)
                ] );

            }
            
        }   
    }
}
