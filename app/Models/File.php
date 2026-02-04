<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель хранения файлов
 */
class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'name',
        'path',
        'type',
        'user_id'
    ];
}
