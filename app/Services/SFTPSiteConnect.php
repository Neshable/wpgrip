<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Tenant;

use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

use Filament\Notifications\Notification;
use phpseclib3\Exception\UnableToConnectException;
use App\Services\GripNotifications;

class SFTPSiteConnect {

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
     * The password for the SSH connection
     *
     * @var string
     */
    private $password = '';

    /**
     * The SFTP connection
     *
     * @var SFTP
     */
    public $sftp;

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
        $tenant = Tenant::find( $this->site->tenant_id );
        
        if ( $tenant )
        {
            // Get the tenant SSH Private.
            try 
            {
                $this->ssh_private = Crypt::decryptString( $tenant->ssh_private );
                $this->password = $tenant->uuid;
            } catch (DecryptException $e) {
                // THrow an error or notification.
                return false;
            }
        }

    }

    public function init()
    {
        // Get this from the server model.
        $this->sftp = new SFTP( $this->ip , $this->port );
        
        // Get the key from DB, decrypt and load.
        $key = PublicKeyLoader::load( $this->ssh_private, $this->password );
    
        // Make the connection.
        try 
        {
            if ( !$this->sftp->login( $this->ssh_user, $key ) ) {
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
        // $this->ssh->enableQuietMode();
        // $this->ssh->enablePTY();
        // $this->ssh->setTimeout(360);     
    }



}