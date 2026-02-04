<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель обратной связи от пользователей
 */
class Feedback extends Model
{

    use SoftDeletes;

    protected $table = "feedback";

    protected $fillable = [
        "user_id",
        "title",
        "text"
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d.m.Y H:i');
    }

    /**
     * Возвращает автора статьи/папки
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class, "user_id"); 
    }
}
