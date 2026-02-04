<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Фиксирует состояние папок (открытых)
 */
class CacheOpenFolders extends Model
{
    protected $table = "cache_open_folders";

    protected $fillable = [
        "user_id", "folder_id", "condition"
    ];
}
