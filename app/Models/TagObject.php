<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель отношений тегов и объектов
 */
class TagObject extends Model
{
    protected $table = 'object_tags';

    protected $fillable = [
        'object_id',
        'tag_id'
    ];
}
