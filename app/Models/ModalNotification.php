<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель для вывода пользователю модального окна 
 * с информацией об изменениях на сайте
 */
class ModalNotification extends Model
{
    protected $table = "user_modal_notification";

    protected $fillable = [
        "user_id",
        "read"
    ];
    
    public $timestamps = false;

    /**
     * возвращает модель пользователя
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class, "user_id");
    }
}
