<?php

namespace App\Enums;

enum RepoStatus: string
{
    case PENDING = 'pending';
    case WORKING = 'working';
    case DONE = 'done';
    case ERROR = 'error';
    case SYNCING = 'syncing';
    case OUT_OF_SYNC = 'out_of_sync';
    case INACTIVE = 'inactive';
    case SUCCESS = 'success';
} 