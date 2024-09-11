<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Enums\SiteStatus;

use Filament\Facades\Filament; 



class Site extends Model
{
    use HasFactory;

    protected $fillable = [
		'name',
		'enabled',
		'url',
        'ssh_user',
        'php_ver',
        'dir_path',
        'uptime_monitor',
        'backup_enabled',
        'files_schedule',
        'db_schedule',
        'is_staging',
        'parent_id',
        'ssh_connection',
        'board_provider',
        'board_url',
        'client_id',
        'server_id',
        'excluded_tables',
        'excluded_files',
        'status',
        'error_log_path'
	];

    protected $casts = [
        'is_staging' => 'boolean',
        'ssh_connection' => 'boolean',
        'excluded_files' => 'array',
        'excluded_tables' => 'array',
        'status' => SiteStatus::class
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            $client->tenant_id = Filament::getTenant()->id;
        });

        // @todo enable
        // static::created(function (Site $site) {

        //     $site_meta = SiteMeta::create([
        //         'site_id' => $site->id,
        //     ]);

        //     // Create monitor model if this is enabled.
        //     if ( $site->uptime_monitor ) {
                
        //         $monitor = UptimeMonitor::where('url', trim($site->url, '/'))->first();

        //         if ( !$monitor ) {
                    
        //             $monitor = UptimeMonitor::create([
        //                 'url' => trim($site->url, '/'),
        //                 'look_for_string' => '',
        //                 'uptime_check_method' => 'head',
        //                 'certificate_check_enabled' => true,
        //                 'site_id' =>  $site->id,
        //                 'uptime_check_interval_in_minutes' => config('uptime-monitor.uptime_check.run_interval_in_minutes'),
        //             ]);

        //             if ( $monitor->id ) {
        //                 // Save both models.
        //                 $site->monitor_id =  $monitor->id;
        //                 $site->save();
        //                 // Later decide which to remove
        //                 $monitor->site_id =  $site->id;
        //                 $monitor->save();
        //                 // Check the uptime
        //                 Artisan::call('monitor:check-uptime');

        //             }
        //         }

        //     }  else {

        //         $monitor = UptimeMonitor::where('url', trim($site->url, '/'))->first();
        //         if ( $monitor ) {
        //             $monitor->delete();
        //             $site->monitor_id = null;
        //             $site->save();
        //         }
                

        //         if (! $monitor) {
        //             // $this->error("Monitor {$url} is not configured");
        //             // return;
        //         }

        //     }

        // });
    }

    /**
     * Get the client that owns this site
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * In case of staging, get the parent production.
     *
     * @return void
     */
    public function parent()
    {
        return $this->belongsTo(Site::class, 'parent_id');
    }

    /**
     * In case of production, get all children stagings.
     *
     * @return void
     */
    public function children()
    {
        return $this->hasMany(Site::class, 'parent_id');
    }

    /**
     * In case of production, get all children stagings.
     *
     * @return void
     */
    // public function monitors()
    // {
    //     return $this->hasMany(UptimeMonitor::class);
    // }


    // /**
    //  * Get the monitor for this site.
    //  */
    // public function monitor(): BelongsTo
    // {
    //     return $this->BelongsTo(Monitor::class);
    // }



     /**
     * Get the owner of this site
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the servers that owns this site
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the backups for this site.
     */
    // public function backups(): HasMany
    // {
    //     return $this->hasMany(Backup::class);
    // }


    /**
     * Get the Repositories
     */
    public function repositories()
    {
        return $this->belongsToMany(Repository::class, 'site_repositories')
                    ->withPivot(['id', 'path', 'branch'])
                    ->withTimestamps();
    }

    /**
     * Display the php logs
     *
     */
    // public function phpLogs()
    // {
    //     return $this->hasMany(PHPLog::class);
    // }

    /**
     * Get the plugins for this site.
     */
    // public function plugins(): BelongsToMany
    // {
    //     return $this->belongsToMany(Plugin::class, 'plugin_site')
    //         ->withPivot( ['version', 'update_version', 'status'] );
    // }

    /**
     * Get the active plugins.
     */
    // public function activePlugins()
    // {
    //     return $this->hasMany(Plugin::class)->where('status', 'active');
    // }

    /**
     * Modify the ssh connection status - either connected or not.
     *
     * @param boolean $status
     * @return void
     */
    public function setConnectionStatus( bool $status = false )
    {
        $this->ssh_connection = $status;
        return $this->save();
    }

    /**
     * Get the status
     *
     * @param boolean $status
     * @return void
     */
    public function getConnectionStatus()
    {
        return $this->ssh_connection;
    }

    /**
     * Get formated size from MB to GB
     *
     * @return void
     */
    public function getFormatedDBSize()
    {
        if ( !$this->dir_size )
        {
            return;
        }

    
        if ($this->dir_size >= 1024)
        {
            return number_format($this->dir_size / 1024, 2) . ' GB';
        }
        else
        {
            return $this->dir_size . ' MB';
        }

        return '0';
    }

    /**
     * Get the db size
     * It's always in MB in the database.
     *
     * @return void
     */
    public function getDBSize()
    {
        if ( !$this->sitemeta )
        {
            return;
        }
        if ($this->sitemeta->db_size >= 1024)
        {
            return number_format($this->sitemeta->db_size / 1024, 2) . ' GB';
        }
 
        else
        {
            return $this->sitemeta->db_size . ' MB';
        }
    }


}
