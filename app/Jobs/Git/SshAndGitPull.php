<?php

namespace App\Jobs\Git;

use App\Models\Repository;
use App\Models\RepoCommit;
use App\Models\Site;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use App\Events\Git\GitPullSuccess;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class SshAndGitPull implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * The Site instance with pivot.
     *
     * @var \Illuminate\Database\Eloquent\Relations\Pivot
     */
    public $pivot;

    /**
     * The Repository instance.
     *
     * @var \App\Models\Repository
     */
    public $repository;

    /**
     * The last commit output
     *
     * @var void|string
     */
    public $commit;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Repository $repository, Site $site )
    {
        // Get original production site.
        $this->repository = $repository;
        $this->site = $site;
       
    }


    public function get_lastest_git_commits()
    {
        return 'git log -n 1 --pretty=format:\'{%n  "commit": "%H",%n  "abbreviated_commit": "%h",%n  "tree": "%T",%n  "abbreviated_tree": "%t",%n  "parent": "%P",%n  "abbreviated_parent": "%p",%n  "refs": "%D",%n  "encoding": "%e",%n  "subject": "%s",%n  "sanitized_subject_line": "%f",%n  "body": "%b",%n  "commit_notes": "%N",%n  "verification_flag": "%G?",%n  "signer": "%GS",%n  "signer_key": "%GK",%n  "author": {%n    "name": "%aN",%n    "email": "%aE",%n    "date": "%aD"%n  },%n  "commiter": {%n    "name": "%cN",%n    "email": "%cE",%n    "date": "%cD"%n  }%n},\' | sed "$ s/,$//" | sed \':a;N;$!ba;s/\r\n\([^{]\)/\\n\1/g\'| awk \'BEGIN { print("[") } { print($0) } END { print("]") }\'';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Cache the pivot table in the class constructor.
        $temp_site = $this->repository->sites->firstWhere('id', $this->site->id);
      
        if( $temp_site ) 
        {
            $this->pivot = $temp_site->pivot;
        } else {
            return false;
        }
        
        if ( !$this->repository ) 
        {
            return false;
        }

        if (!$this->site) 
        {
            GripNotifications::getCustomFailure('No site connected to this repo.');
            return false;
        }
        

        $server = $this->site->server;

        if (!$server) 
        {
            GripNotifications::getCustomFailure('No server assigned.');
            return false;
        }
   
        // Init a new connection to websites's production server.
        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) 
        {
            GripNotifications::getUnauthorizedNotificaiton();
            $this->saveToDb(true);
            $connection->close();
            return false;
        }
   
        if (!$this->isDirectoryAndGitValid($connection)) 
        {
            // Now we have the option to clone the new repo.
            $output = $this->gitCloneNewRepo( $connection );

            $success = $connection->getExitStatusBool();
   
            $this->create_initial_commit_log();
            $connection->close();
    
            $this->saveToDb($success);
    
            $success 
                ? GripNotifications::getGitPulledSuccess()
                : GripNotifications::getGitPulledFailed();

            return true;
        }
        // @todo @doesNotPerformAssertions
        // Sometime the pull actions and all actions will fail due to missmatch in the known host fingerprint
        // currently you need to manually login and clear the lines in known host
        // ultimately there should be automated way.

        // Check if repo is up to date.      
        if ( $this->isRepositoryHaveUpdate($connection) ) 
        {
            GripNotifications::getGitUpToDate();
            // Dispatch event for git pull success
            // event(new GitPullSuccess( $this->repository ) );
            $connection->close();
            return false;
        } 
        
        // Otherwise proceed with normal deploy.
        $output = $this->executeGitCommand($connection);
        
        $success = $connection->getExitStatusBool();
        
        $this->create_commit_log($connection);
        $connection->close();
    
        $this->saveToDb($success);

        if ( $success )
        {
            // Dispatch event for git pull success
            event(new GitPullSuccess( $this->repository ) );

            // dispatch user notification.
            GripNotifications::getGitPulledSuccess();
        }
        else
        {
            GripNotifications::getGitPulledFailed();
        }
   

   
        return true;  
    }

    public function isDirectoryAndGitValid($connection)
    {
        $checkCommands = [
            'if [ -d "' . $this->pivot->path . '" ]; then echo "DIR_EXISTS"; else echo "DIR_NOT_EXISTS"; fi',
            'if [ -d "' . $this->pivot->path . '/.git" ]; then echo "GIT_EXISTS"; else echo "GIT_NOT_EXISTS"; fi',
            'cd ' . $this->pivot->path . ' && git rev-parse --abbrev-ref HEAD'
        ];
    
        foreach ($checkCommands as $key => $command) 
        {
            $output = $connection->exec($command);
            if ($key < 2 && trim($output) !== "DIR_EXISTS" && trim($output) !== "GIT_EXISTS") {
                return false;
            }
            
            if ($key == 2 && trim($output) !== $this->pivot->branch) {
                // sync the git remote with all branches
                $connection->exec('cd ' . $this->pivot->path . ' && git fetch --all');
                // do git checkout to the `$this->pivot->branch`
                $connection->exec('cd ' . $this->pivot->path . ' && git checkout ' . $this->pivot->branch);
            }
        }
    
        return true;
    }

    /**
     * Check if repository is up to date.
     *
     * @param [type] $connection
     * @return boolean
     */
    public function isRepositoryHaveUpdate($connection)
    {
        // Fetch the data from the remote repository and check the status
        $checkCommand = 'cd ' . $this->pivot->path . ' && git fetch && git status';

        // Execute the command
        $status = $connection->exec($checkCommand);

        // If the status message contains 'up to date with', the local repository is  up to date
        return strpos($status, 'up to date with');
    }

    /**
     * This is the main command for new repo that should be pulled if no directory is found
     *
     * @param [type] $connection
     * @return void
     */
    public function gitCloneNewRepo($connection)
    {
        $directoryCreated = $connection->exec('mkdir ' . $this->pivot->path);
    
        // Navigate to the repository directory and clone the remote repository.
        $changeDirectoryCommand = 'cd ' . $this->pivot->path;
        $cloneRepositoryCommand = $changeDirectoryCommand . ' && git clone --depth 1 --no-single-branch ' . $this->repository->remote . ' .';
        // Execute the command and store the result.

        $connection->ssh->disableQuietMode();
        return $connection->exec($cloneRepositoryCommand);
        
    }
    
    /**
     * This is the main command for git pull
     *
     * @param [type] $connection
     * @return void
     */
    public function executeGitCommand($connection)
    {

        // If this is the first time we do pull. @todo figure out.
        $this->checkGitConfigs( $connection );

        $gitCommand = 'cd ' . $this->pivot->path . ' && git add .; git commit -a -m "Commit to preserve
        local changes"; git pull origin ' . $this->pivot->branch . ' -X theirs; if [ $? -eq 0 ]; then exit 0; else exit 1; fi';
        // $connection->ssh->disableQuietMode();
     
        return $connection->exec($gitCommand);
    }

    /**
     * Check if we have user.email and user.name to avoid issues when pulling.
     *
     * @param [type] $connection
     * @return void
     */
    public function checkGitConfigs( $connection )
    {
        // Get the current settings
        $gitEmail = $connection->exec('cd ' . $this->pivot->path . ' && git config --get user.email');
        $gitName = $connection->exec('cd ' . $this->pivot->path . ' && git config --get user.name');

        // If email is not configured, set it
        if (empty($gitEmail)) {
            $setGitEmail = 'cd ' . $this->pivot->path . ' && git config user.email "you@wpgrip.com"';
            $connection->exec($setGitEmail);
        }

        // If name is not configured, set it
        if (empty($gitName)) {
            $setGitName = 'cd ' . $this->pivot->path . ' && git config user.name "WPGrip"';
            $connection->exec($setGitName);
        } 
        
        return true;
    }

    /**
     * Save the result in the DB
     * In case of $error - we make the connection inactive.
     *
     * @return void
     */
    public function saveToDb( $error = false )
    {
        if ( $error ) 
        {
            $this->repository->is_active = false;
        }
        else 
        {
            $this->repository->is_active = true;
            $this->repository->last_pull = Carbon::now();
        }
        
        $this->repository->save();

    }

    /**
     * TODO FIX THIS
     *
     * @param SSHSiteConnect $connection
     * @return void
     */
    public function create_commit_log( SSHSiteConnect $connection )
    {
       
        $last_commits = 'cd ' . $this->pivot->path . ' && ' . $this->get_lastest_git_commits();
        
        $commit_response = json_decode( $connection->exec( $last_commits ) );

        if ( $commit_response && is_array(  $commit_response  ) )
        {
            // Check if we have the same commit.
            $repoCommit = RepoCommit::where('commit', $commit_response[0]->commit)
            ->where('pivot_id', $this->pivot->id)
            ->first();

            // If not create it.
            if ($repoCommit === null) {
                // Create a new record
                $repoCommit = RepoCommit::create([
                    'commit' => $commit_response[0]->commit,
                    'committer' => $commit_response[0]->author->name,
                    'branch' => $this->pivot->branch,
                    'message' => $commit_response[0]->subject,
                    'pivot_id' => $this->pivot->id,
                    'repository_id' => $this->repository->id,
                    'success' => 1
                ]);
            }
           
        }
        
     
    
    }

    /**
     * Create the initial commit when newly repo is cloned
     *
     * @return void
     */
    public function create_initial_commit_log()
    {
        $commit_msg = 'Initial clone';

        $repoCommit = RepoCommit::where('commit', $commit_msg)
            ->where('pivot_id', $this->pivot->id)
            ->first();
        
        if ($repoCommit === null) {
            // Create a new record
            $repoCommit = RepoCommit::create([
                'commit' => $commit_msg,
                'committer' => '',
                'branch' => $this->pivot->branch,
                'message' => $commit_msg,
                'pivot_id' => $this->pivot->id,
                'repository_id' => $this->repository->id,
                'success' => 1
            ]);
        }

    }
}
