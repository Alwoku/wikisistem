<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Теги для статей
 */
class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'name'
    ];
    
}
