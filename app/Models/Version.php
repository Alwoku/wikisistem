<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Контроль изменений контента статей
 */
class Version extends Model
{
    protected $table = 'versions';

    protected $fillable = [
        'object_id',
        'content',
        'user_id',
        'version',
    ];

        
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d.m.Y H:i');
    }
    
    /**
     * Возвращает модель пользователя, создавшего изменения
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class, "user_id");
    }
}
