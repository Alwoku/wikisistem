<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * предлагаемые изменения для статей
 */
class SuggestedChanges extends Model
{
    use SoftDeletes;

    protected $table = "suggested_changes";

    protected $fillable = [
        "object_id",
        "parent_id",
        "name",
        "text",
        "comment",
        "user_id"
    ];


        
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d.m.Y H:i');
    }
    
    
    /**
     * возвращает модель объекта 
     */
    public function object(): BelongsTo{
        return $this->belongsTo(BasisObject::class, "object_id");
    }
    
    /**
     * возвращает модель автора
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class, "user_id");
    }

    public function parentObject(): BelongsTo{
        return $this->belongsTo(BasisObject::class, "parent_id");
    }
}
