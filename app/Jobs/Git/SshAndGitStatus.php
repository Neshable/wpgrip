<?php

namespace App\Jobs\Git;

use App\Models\Repository;
use App\Models\RepoCommit;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SshAndGitStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * The Repository instance.
     *
     * @var \App\Models\Repository
     */
    public $repository;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Repository $repository )
    {
        // Get original production site.
        $this->repository = $repository;
    }

   /**
     * Save the result in the DB
     *
     * @return void
     */
    public function save_to_db()
    {

        $this->repository->last_pull = Carbon::now();
        $this->repository->branch = 'master';
        $this->repository->save();

    }

    public function get_lastest_git_commits()
    {
        $command = 'git log -n 1 --pretty=format:\'{%n  "commit": "%H",%n  "abbreviated_commit": "%h",%n  "tree": "%T",%n  "abbreviated_tree": "%t",%n  "parent": "%P",%n  "abbreviated_parent": "%p",%n  "refs": "%D",%n  "encoding": "%e",%n  "subject": "%s",%n  "sanitized_subject_line": "%f",%n  "body": "%b",%n  "commit_notes": "%N",%n  "verification_flag": "%G?",%n  "signer": "%GS",%n  "signer_key": "%GK",%n  "author": {%n    "name": "%aN",%n    "email": "%aE",%n    "date": "%aD"%n  },%n  "commiter": {%n    "name": "%cN",%n    "email": "%cE",%n    "date": "%cD"%n  }%n},\' | sed "$ s/,$//" | sed \':a;N;$!ba;s/\r\n\([^{]\)/\\n\1/g\'| awk \'BEGIN { print("[") } { print($0) } END { print("]") }\'';
        return $command;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->repository ) {
            
            // Check if we have a site assigned just in case.
            if ( $site = $this->repository->site )
            {
                
                // Great, so now we need to check for the server.
                if ( $server = $this->repository->site->server )
                {
                    // Init a new connection to websites's production server.
                    $connection = new SSHSiteConnect( $site );
                    // If we don't have connection abort and send notification.
                    if ( !$connection->active ) 
                    {
                        GripNotifications::getUnauthorizedNotificaiton();

                        return false;
                    }
                    // @todo extract to global commands!
                    $git_cmd = 'cd ' . $this->repository->path . ' && ' . $this->get_lastest_git_commits();

                    $connection->ssh->disableQuietMode();
                    // Let's login and run the WP Cli command.
                    $output = $connection->exec( $git_cmd );
                    $this->repository->commits = json_decode($output);

                 
                    // test here
                    // $this->repository->branch = 'master';
                    $this->repository->save();
                    
                    

                    $success = $connection->getExitStatusBool();
            
                    $connection->close();
                   
                    // All went well.
                    if ( $output && $success ) {
                        $this->save_to_db();
                        GripNotifications::getGitPulledSuccess();
                    }
                    else {
                        GripNotifications::getGitPulledFailed();
                    }
                    
                }
                else
                {
                    // Notification thath there is no server there.
                    GripNotifications::getCustomFailure('No server assigned.');
                }
            } 
            else
            {
                // Notification that there is no site connected to this repo!
                GripNotifications::getCustomFailure('No site connected to this repo.');
            }

            return true;
        }   
    }
}
