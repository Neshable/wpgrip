<?php

namespace App\Services;

use App\Models\Site;
use App\Models\User;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

use Filament\Notifications\Notification;
use phpseclib3\Exception\UnableToConnectException;
use App\Services\GripNotifications;

class SSHSiteConnect {

    public $site;

    /**
     * The linux user
     *
     * @var string
     */
    public $ssh_user;

    /**
     * The IP
     *
     * @var string
     */
    public $ip;

    /**
     * The Port
     *
     * @var integer
     */
    public $port;

    /**
     * If we have the connection established.
     *
     * @var bool
     */
    public $active = false;

    /**
     * The user private key
     *
     * @var string
     */
    public $ssh_private;

    /**
     * The SSH2 connection
     *
     * @var SSH2
     */
    public $ssh;

    /**
     * The
     *
     * @param Site $site
     * @param integer $port
     */
    public function __construct( Site $site )
    {   
        // Cache the site.
        $this->site = $site;
        // Check if we have all the data needed.
        $this->check();
        // Init the connection and save the object.
        $this->init();
    }

    public function check()
    { 
        if ( $this->site->server )
        {
            $this->ip = $this->site->server->ip;
            $this->ssh_user = $this->site->ssh_user;
            $this->port = $this->site->server->port ?? 22;
        }
        
        // Check site owner.
        $user = User::find( $this->site->user_id );
        
        if ( $user )
        {
            // Get the user SSH Private.
            try 
            {
                $this->ssh_private = Crypt::decryptString($user->ssh_private);
            } catch (DecryptException $e) {
                // THrow an error or notification.
                return false;
            }
        }

    }

    public function init()
    {
        // Get this from the server model.
        $this->ssh = new SSH2( $this->ip , $this->port );
        // Get the key from DB, decrypt and load.
        $key = PublicKeyLoader::load($this->ssh_private);
  
        // Make the connection.
        try 
        {
            if ( !$this->ssh->login( $this->ssh_user, $key ) ) {
                // throw new \Exception('Login failed');
                GripNotifications::getUnauthorizedNotificaiton();
            } else {
                $this->active = true;
            }
        } 
        catch ( UnableToConnectException $e) 
        {
            // THrow an error or notification.
            // GripNotifications::getUnauthorizedNotificaiton();
            report($e);
        } 
        catch (\phpseclib3\Exception\ConnectionClosedException | \UnexpectedValueException $e)
        {
            // ...
            report($e);
        }  
        catch( \Exception $e)
        {
            report($e->getMessage());
        }
        finally 
        {
            // ...
        }

        // By default $ssh->exec() returns both stdout and stderr. To suppress stderr you can call QuiteMode
        $this->ssh->enableQuietMode();
        // $this->ssh->enablePTY();
        $this->ssh->setTimeout(360);     
    }

    /**
     * Execute a command
     *
     * @param string $command
     * @return void
     */
    public function exec( string $command )
    {
        return $this->ssh->exec( $command );
    }

    /**
     * Get the exit code to determine if it was success
     * 
     */
    public function getExitStatusBool()
    {
        // $connection->ssh->disableQuietMode();
        $status_code = $this->ssh->getExitStatus();
        

        if ( $status_code == 0 ) 
        {
            return true;
        }
        

        switch( $status_code )
        {
            case 1:
                // Emit a message - 1 Generic error, usually because invalid command line options or malformed configuration
                return false;
                break;
            case 2:
                // Connection failed
                return false;
                break;
            case 75:
                // Disconnected by application
                return false;
                break;
        }

        return false;
    
    }
    
    /**
     * Close remote SSH connection
     *
     * @return void
     */
    public function close()
    {
        $this->ssh->exec('exit');
    }
 

}