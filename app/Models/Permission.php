<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

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
