<?php

namespace App\Services;

use App\Models\Site;
use App\Models\User;
use App\Models\Backup;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Support\Facades\Process;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Filament\Notifications\Notification;

class BackupLocation {

    /**
     * The site object
     *
     * @var Site
     */
    public $site;


    /**
     * The user object
     *
     * @var User
     */
    public $user;

    public $db_name;

    public $file_name;

    /**
     * Current timestamp by Carbon
     */
    public $timestamp;

    /**
     * Sanitized URL for folter/file name
     *
     * @var string
     */
    public $sanitizedUrl = 'sanitized_url';

    /**
     * The
     *
     * @param string $ip
     * @param integer $port
     */
    public function __construct( Site $site, $timestamp = null )
    {
        $this->site = $site;
        $this->user = User::find( $site->user_id );
        // Set the correct timestamp.
        $this->timestamp = $timestamp ?: Carbon::now()->format('YmdHi');
        // Sanitize the site url
        $this->sanitizedUrl = preg_replace( '/[^a-z0-9_\-]/i', '_', parse_url( $site->url, PHP_URL_HOST ) );

        $this->db_name = $this->generateDBBackupName();
        $this->file_name = $this->generateFilesBackupName();
       
    }

    public function generateDBBackupName( string $extension = '.sql.gz' )
    {
        // Concatenate the site ID, sanitized URL, current date, and file extension to create a unique file name.
        return "db_backup_{$this->site->id}_{$this->sanitizedUrl}_{$this->timestamp}{$extension}";
    }

    public function generateFilesBackupName( string $extension = '.tar.gz' )
    {
        // Concatenate the site ID, sanitized URL, current date, and file extension to create a unique file name.
        return "files_backup_{$this->site->id}_{$this->sanitizedUrl}_{$this->timestamp}{$extension}";
    }
    /**
     * Gets relative path file to the local Disk
     * To use directly in the Storage::disk('temp') argument
     *
     * @return string
     */
    public function getRelativeLocalBackupFile()
    {
        return $this->getLocalBackupSitePath( true ) . '/' . $this->file_name;
    }

    
    public function getLocalBackupPath()
    {
        return base_path('/temp_files' );
    }
    /**
     * Gets the local path of where all the backups should be stored and deleted after.
     *
     * @return void
     */
    public function getLocalBackupSitePath( $include_base_path = false )
    {
        // Create a unique directory path for the backup.
        if ( $include_base_path )
        {
            return 'site_' . $this->site->id . '_' . $this->sanitizedUrl;
        }

        return base_path('/temp_files/site_' . $this->site->id . '_' . $this->sanitizedUrl );
    }


    public function getLocalFilesBackupPath()
    {
        // Create a unique directory path for the backup.
        return $this->getLocalBackupSitePath() . '/site_' . $this->site->id . '_' . $this->sanitizedUrl . '_' . $this->timestamp;
    }

    public function getS3BackupPath()
    {
        // Global path for all backups
         $path = $this->user->getUserPath() . "/site-id-" . $this->site->id . "/backups";
 
         return $path;
    }

    /**
     * Get S3 path for storing screenshots
     *
     * @return void
     */
    public function getS3ScreenshotPath()
    {
        // Global path for all backups
         $path = $this->user->getUserPath() . "/site-id-" . $this->site->id . "/screenshots";
 
         return $path;
    }

    public function getS3FilesBackupPath()
    {
        // Global path for all file backups
        return self::getS3BackupPath() . "/files";
    }

    public function getS3DatabaseBackupPath()
    {
        // Global path for all db backups
        return self::getS3BackupPath() . "/db";
    }

    
}