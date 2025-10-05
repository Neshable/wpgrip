<?php

namespace App\Jobs\Git;

use App\Models\Repository;
use App\Models\Deployment;
use App\Models\Site;
use App\Models\User;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use App\Events\Git\GitPullSuccess;

use Carbon\Carbon;
use Deployer\Deployer;
use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

use App\Enums\RepoStatus;

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
     * The status of the repository
     *
     * @var string
     */
    public $status;

    /**
     * The error message
     *
     * @var string|null
     */
    public $status_text;

    /**
     * The last pull date
     *
     * @var string|null
     */
    public $last_pull;

    /**
     * The recipient of the notification
     *
     * @var \App\Models\User|null
     */
    public $recipient;

    /**
     * The type of the deployment
     *
     * @var string
     */
    public $deployment_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Repository $repository, Site $site, User $recipient = null, string $deployment_type = 'manual' )
    {
        // Get original production site.
        $this->repository = $repository;
        $this->site = $site;
        $this->status = RepoStatus::WORKING->value;
        $this->recipient = auth()->user();
        $this->deployment_type = $deployment_type;
    }


    public function get_latest_git_commit()
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

        // If the site is found, set the pivot, otherwise return false and set the status text.
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
            $this->status_text = 'No site connected to this repo.';
            Notification::make()
                ->title('No site connected to this repo.')
                ->danger()
                ->body( $this->status_text ) 
                ->sendToDatabase($this->recipient);
            return false;
        }
        
        $server = $this->site->server;

        if (!$server) 
        {
            $this->status_text = 'No server assigned.';
            GripNotifications::getCustomFailure($this->status_text);
            return false;
        }
 

        // Update the repository status to working.
        $this->repository->update(['status' => RepoStatus::WORKING->value]);
   
        // Init a new connection to websites's production server.
        $connection = new SSHSiteConnect($this->site);
        
        if (!$connection->active) 
        {
            $this->status_text = 'SSH connection failed.';
            $this->status = RepoStatus::ERROR->value;
            // GripNotifications::getUnauthorizedNotificaiton();
            $this->saveToDb();
            $connection->close();
            return false;
        }

        // Check if directory exist, if not then clone new repo.
        if ( !$this->isDirectoryAndGitValid($connection) ) 
        {
            // Disable quite mode in order to pick up errors.
            $connection->ssh->disableQuietMode();
            // Check for public key access
            if ( !$this->checkGitPublicKey( $connection ) ) 
            {
                // GripNotifications::gitNoPublicKey();
                $this->status_text = 'No public key found.';
                $this->status = RepoStatus::ERROR->value;
                $this->saveToDb();
                $connection->close();
                return false;
            }

            // Now we have the option to clone the new repo.
            $output = $this->gitCloneNewRepo( $connection );

            $success = $connection->getExitStatusBool();

            // Get the comit hash and store it.
            $this->create_commit_log($connection);

            $connection->close();
    
            if ( $success ) 
            {
                // Update the repository status to success.
                $this->status = RepoStatus::SUCCESS->value;
                $this->status_text = 'Git clone success.';
                $this->saveToDb();
                // GripNotifications::getGitPulledSuccess();
            }
            else
            {
                $this->status = RepoStatus::ERROR->value;
                $this->status_text = 'Git clone failed.';
                $this->saveToDb();
                // GripNotifications::getGitPulledFailed();
            }
 

            return true;
        }

        // @todo @doesNotPerformAssertions
        // Sometime the pull actions and all actions will fail due to missmatch in the known host fingerprint
        // currently you need to manually login and clear the lines in known host
        // ultimately there should be automated way.

        // Check if repo is up to date.      
        if ( $this->isRepositoryHaveUpdate($connection) ) 
        {
            $this->status = RepoStatus::SUCCESS->value;
            $this->status_text = 'Repository is up to date.';
            $this->last_pull = Carbon::now();
            $this->saveToDb();
            // GripNotifications::getGitUpToDate();
            $connection->close();
            return false;
        } 
        
        // Otherwise proceed with normal deploy.
        $output = $this->executeGitCommand($connection);
        
        $success = $connection->getExitStatusBool();

        // Get the comit hash and store it.
        $this->create_commit_log($connection);

        $connection->close();
    
        if ( $success )
        {
            // Dispatch event for git pull success
            event(new GitPullSuccess( $this->repository ) );
            // Save the db
            $this->status = RepoStatus::SUCCESS->value;
            $this->status_text = 'Git pull success.';
            $this->last_pull = Carbon::now();
            $this->saveToDb();
            // dispatch user notification.
            Notification::make()
                ->title('Git pull success.')
                ->success()
                ->body( $this->status_text ) 
                ->sendToDatabase($this->recipient);

        }
        else
        {
            // Pass true for errors.
            $this->status = RepoStatus::ERROR->value;
            $this->status_text = 'Git pull failed.';
            $this->saveToDb();
            // GripNotifications::getGitPulledFailed();
        }
   
        return true;  
    }

    public function isDirectoryAndGitValid($connection)
    {
        $checkCommands = [
            'if [ -d "' . $this->getAbsolutePathToDeploy() . '" ]; then echo "DIR_EXISTS"; else echo "DIR_NOT_EXISTS"; fi',
            'if [ -d "' . $this->getAbsolutePathToDeploy() . '/.git" ]; then echo "GIT_EXISTS"; else echo "GIT_NOT_EXISTS"; fi',
            'cd ' . $this->getAbsolutePathToDeploy() . ' && git rev-parse --abbrev-ref HEAD'
        ];
    
        foreach ($checkCommands as $key => $command) 
        {
            $output = $connection->exec($command);
            if ($key < 2 && trim($output) !== "DIR_EXISTS" && trim($output) !== "GIT_EXISTS") {
                return false;
            }
            
            if ($key == 2 && trim($output) !== $this->pivot->branch) {
                // sync the git remote with all branches
                $connection->exec('cd ' . $this->getAbsolutePathToDeploy() . ' && git fetch --all');
                // do git checkout to the `$this->pivot->branch`
                $connection->exec('cd ' . $this->getAbsolutePathToDeploy() . ' && git checkout ' . $this->pivot->branch);
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
        $checkCommand = 'cd ' . $this->getAbsolutePathToDeploy() . ' && git fetch && git status';

        // Execute the command
        $status = $connection->exec($checkCommand);

        // If the status message contains 'up to date with', the local repository is  up to date
        return strpos($status, 'up to date with');
    }

    /**
     * Return the absolute path to the repo.
     *
     * @return void
     */
    public function getAbsolutePathToDeploy()
    {
        return rtrim( $this->site->dir_path, '/' ) . '/' . $this->pivot->path;
    }

    /**
     * This is the main command for new repo that should be pulled if no directory is found
     *
     * @param [type] $connection
     * @return void
     */
    public function gitCloneNewRepo( $connection )
    {
        
        $directoryCreated = $connection->exec('mkdir ' . $this->getAbsolutePathToDeploy());
    
        // Navigate to the repository directory and clone the remote repository.
        $changeDirectoryCommand = 'cd ' . $this->getAbsolutePathToDeploy();
        $cloneRepositoryCommand = $changeDirectoryCommand . ' && git clone --depth 1 --no-single-branch ' . $this->repository->remote . ' .';
        // Execute the command and store the result.

       
        return $connection->exec($cloneRepositoryCommand);
        
    }

    /**
     * Check if the public key is added
     *
     * @param  [type] $connection
     * @return void
     */
    public function checkGitPublicKey( $connection )
    {
        // Parse the remote to extract git@github.com or similar.
        $url = str_replace(":", "/", $this->repository->remote ); // convert ':' to '/'
        $parts = parse_url("ssh://" . $url); 

        // Full part.
        $output = $connection->exec('cd ' . dirname( $this->getAbsolutePathToDeploy() ) . ' && ssh -T ' . $parts['user'] . "@" . $parts['host'] );
   
        if ( preg_match('/Permission denied/', $output) || !$connection->getExitStatusBool() ) 
        {
            return false;
        }

        return true;

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

        $gitCommand = 'cd ' . $this->getAbsolutePathToDeploy() . ' && ' .
        'git fetch origin ' . $this->pivot->branch . ' && ' .  // Fetch the latest changes
        'git reset --hard origin/' . $this->pivot->branch; // Reset local branch to remote state
        // 'git clean -fd'; // Remove untracked files and directories

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
        $gitEmail = $connection->exec('cd ' . $this->getAbsolutePathToDeploy() . ' && git config --get user.email');
        $gitName = $connection->exec('cd ' . $this->getAbsolutePathToDeploy() . ' && git config --get user.name');

        // If email is not configured, set it
        if (empty($gitEmail)) {
            $setGitEmail = 'cd ' . $this->getAbsolutePathToDeploy() . ' && git config user.email "you@wpgrip.com"';
            $connection->exec($setGitEmail);
        }

        // If name is not configured, set it
        if (empty($gitName)) {
            $setGitName = 'cd ' . $this->getAbsolutePathToDeploy() . ' && git config user.name "WPGrip"';
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
        if( $this->pivot ) {
            $this->repository->sites()->updateExistingPivot($this->site->id, [
                'status' => $this->status,
                'status_text' => $this->status_text,
                'last_pull' => Carbon::now()
            ]);
        }

    }

    /**
     * TODO FIX THIS
     *
     * @param SSHSiteConnect $connection
     * @return void
     */
    public function create_commit_log( SSHSiteConnect $connection )
    {
       
        $last_commits = 'cd ' . $this->getAbsolutePathToDeploy() . ' && ' . $this->get_latest_git_commit();
        
        $commit_response = json_decode( $connection->exec( $last_commits ) );

        if ( $commit_response && is_array(  $commit_response  ) )
        {
            $repoCommit = Deployment::firstOrCreate(
                ['commit' => $commit_response[0]->commit, 'pivot_id' => $this->pivot->id],
                [
                    'committer' => $commit_response[0]->author->name,
                    'branch' => $this->pivot->branch,
                    'message' => $commit_response[0]->subject,
                    'site_id' => $this->pivot->site_id,
                    'repository_id' => $this->repository->id,
                    'success' => 1,
                    'type' => $this->deployment_type
                ]
            );
           
        }
    
    }

    
}
