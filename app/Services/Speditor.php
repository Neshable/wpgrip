<?php

namespace App\Services;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Speditor {

    /**
     * The site object
     *
     * @var Site
     */
    public $site;

    /**
     * Current timestamp by Carbon
     *
     * @var Carbon|null
     */
    public $timestamp;

    /**
     * Sanitized URL for folder/file name
     *
     * @var string
     */
    public $sanitizedUrl = 'sanitized_url';

    /**
     * Constructor
     *
     * @param Site $site The site object
     * @param Carbon|null $timestamp Optional timestamp
     */
    public function __construct( Site $site, $timestamp = null )
    {
        $this->site = $site;
        $this->timestamp = $timestamp;
        $this->sanitizedUrl = preg_replace( '/[^a-z0-9_\-]/i', '_', parse_url( $site->url, PHP_URL_HOST ) );
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

    public function getS3BackupPath()
    {
        // Global path for all backups
        $path = $this->site->tenant->getTenantPath() . '/site-id-' . $this->site->id . '/backups';
 
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
        $path = $this->site->tenant->getTenantPath() . '/site-id-' . $this->site->id . '/screenshots';
 
        return $path;
    }

    public function getS3FilesBackupPath()
    {
        // Global path for all file backups
        return self::getS3BackupPath() . '/files';
    }

    public function getS3DatabaseBackupPath()
    {
        // Global path for all db backups
        return self::getS3BackupPath() . '/db';
    }
}