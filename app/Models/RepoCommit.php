<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepoCommit extends Model
{
    use HasFactory;

    protected $fillable = [
        'committer',
        'repository_id',
        'branch',
        'commit',
        'pivot_id',
        'message',
        'success'
    ];


    /**
     * Get the post that owns the comment.
     */
    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }
}
