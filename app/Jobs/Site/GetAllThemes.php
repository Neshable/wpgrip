<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Models\Theme;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;
use App\Services\WPCliService;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetAllThemes implements ShouldQueue
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
    public function save_to_db( $theme_list )
    {
        $decoded_json = json_decode($theme_list);

        foreach ( $decoded_json as $single_theme )
        {
            // Find the plugin with our unique slug or create new.
            $new_theme = Theme::firstOrCreate(
                ['name' => $single_theme->name ],
                ['title' => $single_theme->title, 'description' => $single_theme->description ]
            );
            
            if ( $new_theme )
            {
                // If our record exist in the pivot table then update.
                if( $this->site->themes()->find($new_theme->id) )
                {
                    $this->site->themes()->updateExistingPivot( $new_theme->id, array(
                                'version' => $single_theme->version,
                                'update_version' => $single_theme->update_version,
                                'status' => $single_theme->status
                        ) );
                }
                else
                {
                    // Otherwise attach the plugin in the pivot table with the attributes.
                    $this->site->themes()->attach( $new_theme->id, array(
                            'version' => $single_theme->version,
                            'update_version' => $single_theme->update_version,
                            'status' => $single_theme->status
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
            $output = $connection->exec('cd ' . $this->site->dir_path . ' && ' . WPCliService::getAllThemes() );
           
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
