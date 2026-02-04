<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Разрешение/запрет на загрузку определенных расширений файлов
 */
class ExpansionFile extends Model
{
    protected $table = 'expansion_files';

    protected $fillable = [
        'type',
        'expansion',
        'upload'
    ];


    protected $hidden = [
        "updated_at",
        "created_at",
    ];
}
