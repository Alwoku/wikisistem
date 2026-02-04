<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Контент статей
 */
class Content extends Model
{

    use SoftDeletes;

    protected $table = 'contents';

    protected $fillable = [
        'object_id',
        'content',
    ];

    
}
