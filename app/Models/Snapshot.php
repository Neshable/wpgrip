<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Snapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'enabled' ,
        'file_path',
        'backup_id',
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
}
