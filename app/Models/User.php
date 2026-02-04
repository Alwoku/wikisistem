<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\SyncMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
   
    use HasApiTokens, HasFactory, Notifiable, SyncMany;

    protected $table = "users";

    protected $fillable = [
        'name',
        "password",
        'is_active',
        'is_admin',
        'basis_active',
        'panel_pin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'basis_active' => 'boolean'
    ];
    
    /**
     * Список групп
     */
    public function groups(): HasMany{
        return $this->hasMany(UserGroup::class, "user_id");
    }
    
    /**
     * Возвращает список открытых пользователем папок
     */
    public function openFolders() : HasMany {   
        return $this->hasMany(CacheOpenFolders::class, "user_id");
    }
    
    /**
     * возвращает модель значения прочитана ли информация об изменениях
     * на сайте пользователем
     */
    public function modalNotification(): HasOne{
        return $this->hasOne(ModalNotification::class, "user_id");
    }


    /**
     * возвращает список с предложенными изменениями
     */
    public function suggestedChanges(): HasMany{
        return $this->hasMany(SuggestedChanges::class, "user_id");
    }
}
