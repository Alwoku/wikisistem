<?php

namespace App\Models;

use App\SyncMany;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    use SyncMany;

    protected $table = "groups";

    protected $fillable = [
        "name",
        "default_index",    
        "user_id",
        "author",
    ];

    public function users(){
        return $this->hasMany(UserGroup::class);
    }

    public function objects(){
        return $this->hasMany(Permission::class, "group_id");
    }
}
