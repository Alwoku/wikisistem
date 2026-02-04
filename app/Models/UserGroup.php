<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель таблицы отношений пользователь группа
 */
class UserGroup extends Model
{
    protected $table = "user_group";

    protected $fillable = [
        "user_id",
        "group_id"
    ];

    public $timestamps = false;

    public function group(){
        return $this->belongsTo(Group::class, "group_id");
    }

    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
}
