<?php

namespace App\Services;
 
class WPCliService {

    public static $allowedDbActions = ['reset', 'prefix', 'repair', 'optimize'];

    // Return empty string if it doesn't exist.
    public static function checkIfWPCliExists()
    {
        return 'which wp';
    }
    public static function getAllPlugins()
    {
        return 'wp plugin list --fields=name,status,update,version,update_version,update_package,title,description --format=json';
    }

    public static function getAllThemes()
    {
        return 'wp theme list --fields=name,status,update,version,update_version,update_package,title,description --format=json';
    }

    public static function getConfigConstants()
    {
        return 'wp config get --format=json';
    }

    public static function getAllWPConfigConstants()
    {
        return 'wp config list WP_ --format=json';
    }

    public static function deactivatePlugin( string $plugin )
    {
        return 'wp plugin deactivate ' . $plugin;
    }

    public static function activatePlugin( string $plugin )
    {
        return 'wp plugin activate ' . $plugin;
    }

    public static function backupDb( string $dir_path )
    {
        return 'cd ' . $dir_path . ' && wp db export - | gzip -9';
    }

    public static function getCoreVersion()
    {
        return 'wp core version';
    }

    public static function updateCoreVersion( bool $minor = false, string $version = '', bool $force = false )
    {
        $command = 'wp core update';

       if ($minor) 
       {
           $command .= ' --minor';
           return $command;
       }
       
       if ($version && $version != '') 
       {
           $command .= " --version=$version";
       }
       
       if ($force) {
           $command .= ' --force';
       }

       return $command;
    }

    public static function getCliVersion()
    {
        return 'wp cli version';
    }

    public static function getDBSize( $format = 'mb' )
    {
        return 'wp db size --size_format=' . $format;
    }

    public static function getDBPrefix()
    {
        return 'wp db prefix';
    }   

    public static function DBAction( string $action )
    {
        if ( !in_array($action, self::$allowedDbActions) ) 
        {
            return;
            // Handle the error - either by throwing an exception or another means of error handling.
            // throw new InvalidArgumentException("The action '{$action}' is not allowed.");
        }
    
        return 'wp db ' . $action;
    }   

    public static function getAllDBTables( $format = 'mb' )
    {
        return 'wp db size --all-tables --size_format=' . $format . ' --json';
    }

    public static function clearCache( string $name )
    {
        switch ( $name )
        {
            case 'wprocket':
                return 'wp rocket clean --confirm';
            case 'object':
                return 'wp cache flush';
            case 'beaver':
                return 'wp beaver clearcache';
            case 'autoptimize':
                return 'wp autoptimize clear';
            case 'cache_enabler':
                return 'wp cache-enabler clear';
            case 'fastest_cache':
                return 'wp fastest-cache clear all';
            case 'w3_total_cache':
                return 'wp w3-total-cache flush all';
            case 'supercache':
                return 'wp super-cache flush';
            default:
                return '';
        }
    }

    public static function verifyCoreChecksums()
    {
        return 'wp core verify-checksums';
    }

    public static function checkDatabase()
    {
        return 'wp db check';
    }

    public static function listUsers()
    {
        return 'wp user list --fields=ID,user_login,user_email,roles --format=table';
    }

    public static function flushRewrites()
    {
        return 'wp rewrite flush --hard';
    }
 
}