<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Автосохранение контента статьи
 */
class AutoSave extends Model
{
    protected $table = 'autosave';

    protected $fillable = [
        'object_id',
        'user_id',
        'name',
        'content',
        'parents_ids'
    ];
    
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d.m.Y H:i');
    }
}
