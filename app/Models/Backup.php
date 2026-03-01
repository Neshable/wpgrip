<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $attributes = [
        'type' => 'db',
    ];

    protected $fillable = [
        'site_id' ,
        'provider',
        'checksum',
        'file_path',
        'type',
        'frequency',
        'size',
        'status',
        'excluded_tables',
        'excluded_files',
        'delete_date',
        'frequency',
        'retention_days',
        'last_backup',
        'next_backup',
        'tenant_id'
	];


    /**
     * Get the post that owns the comment.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function snapshots()
    {
        return $this->hasMany(Snapshot::class);
    }

    

      /**
     * Get the owner of this site
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }


}
