<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Models\Plugin;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetAllPlugins implements ShouldQueue
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
     * @return void
     */
    public function __construct( Site $site )
    {
        // Get original production site.
        $this->site = $site;
    }

    /**
     * Save the result in the DB
     *
     * @return void
     */
    public function save_to_db( $plugin_list )
    {
        $decoded_json = json_decode($plugin_list);

        foreach ( $decoded_json as $single_plugin )
        {
            // Find the plugin with our unique slug or create new.
            $new_plugin = Plugin::firstOrCreate(
                ['name' => $single_plugin->name ],
                ['title' => $single_plugin->title, 'description' => $single_plugin->description ]
            );
            
            if ( $new_plugin )
            {
                // If our record exist in the pivot table then update.
                if( $this->site->plugins()->find($new_plugin->id) )
                {
                    $this->site->plugins()->updateExistingPivot( $new_plugin->id, array(
                                'version' => $single_plugin->version,
                                'update_version' => $single_plugin->update_version,
                                'status' => $single_plugin->status
                        ) );
                }
                else
                {
                    // Otherwise attach the plugin in the pivot table with the attributes.
                    $this->site->plugins()->attach( $new_plugin->id, array(
                            'version' => $single_plugin->version,
                            'update_version' => $single_plugin->update_version,
                            'status' => $single_plugin->status
                    ) );
                }

                
            }
        }

        // Update date last sync.

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site ) {

            // Init a new connection to websites's production server.
            $connection = new SSHSiteConnect( $this->site );
            // If we don't have connection abort and send notification.
            if ( !$connection->active ) 
            {
                GripNotifications::getUnauthorizedNotificaiton();

                return false;
            }

            // Let's login and run the WP Cli command.
            $output = $connection->exec('cd ' . $this->site->dir_path . ' && wp plugin list --fields=name,status,update,version,update_version,update_package,title,description --format=json 2> /dev/null');
           
            $connection->close();
            
            if ( $output ) {
                 // Clean the JSON from any warning messages we may have.
                 // @todo extract to helper method.
                preg_match("/\[[^\]]*\]/", $output, $matches);
                $this->save_to_db($matches[0]);
            }
            

            GripNotifications::getDatabaseSyncedNotification();
            return true;
        }   
    }
}
