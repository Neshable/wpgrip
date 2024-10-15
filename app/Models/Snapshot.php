<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Snapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'enabled',
        'status',
        'local_path',
        'remote_path',
        'backup_id',
        'type',
        'tenant_id',
        'size',
        'frequency',
        'size',
	];


    /**
     * Get the post that owns the comment.
     */
    public function backup()
    {
        return $this->belongsTo(Backup::class);
    }

    /**
     * Get the owner of this site
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Return the path to the file in S3
     *
     * @return void
     */
    public function getS3path() 
    {
        if ( $this->local_path && $this->remote_path ) {
            return $this->remote_path . '/' . basename( $this->local_path );
        }

        return false;

    }
}
