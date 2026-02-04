<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * модель избранных/закладок объектов у пользователей
 */
class UserFavorites extends Model
{
    protected $table = "user_favorites";

    protected $fillable = [
        "user_id", "object_id"
    ];
    
    /**
     * Возвращает модель пользоавтеля
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    
    /**
     * Возвращает модель объекта
     */
    public function object(): BelongsTo{
        return $this->belongsTo(BasisObject::class);
    }
}
