<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * история изменений на сайте
 */
class HistoryOfChanges extends Model
{
    protected $table = "history_of_changes";

    protected $fillable = [
        "version",
        "info",
        "admin_info",
        "active",
    ];
}
