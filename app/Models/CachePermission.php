<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Кеш таблица для прав доступа
 */
class CachePermission extends Model
{
    protected $table = 'cache_permissions';

    protected $fillable=[
        'object_id',
        'group_id',
        'create',
        'read',
        'update',
        'delete',
        'recursive_id'
    ];
}
