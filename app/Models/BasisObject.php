<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель объектов базиса (папки, статьи)
 * типы: folder, note
 */
class BasisObject extends Model
{
    use SoftDeletes;

    protected $table = 'objects';

    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'user_id',
        'version'
    ];
    
    /**
     * Возвращает модель родителя
     *
     */
    public function parent(): BelongsTo{
        return $this->belongsTo(static::class, "parent_id");
    }
    
    /**
     * children
     */
    public function children(): HasMany{
        return $this->hasMany(static::class, "parent_id");   
    }
        
    /**
     * Возвращает модель контента статьи
     */
    public function content(): HasOne{
       return $this->hasOne(Content::class, "object_id"); 
    }
    
    /**
     * Возвращает автора статьи/папки
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class, "user_id"); 
    }
        
    /**
     * возвращает список пользователей,
     * у которых объект находится в избранном
     */
    public function usersFavorites(): HasMany{
        return $this->hasMany(UserFavorites::class, "object_id");
    }
    
    /**
     * возвращает список с предложенными изменениями
     */
    public function suggestedChanges(): HasMany{
        return $this->hasMany(SuggestedChanges::class, "object_id");
    }

    /**
     * возвращает массив с данными для хлебных крошек
     *
     * @return array
     */
    public function createdBreadcrumbs(){
        $result = [];

        $allParentsIds = $this->allParentsIds();


        $allObjects = static::whereIn("id", $allParentsIds)->get();

        foreach ($allParentsIds as $id) {
            
            $object = $allObjects->firstWhere("id", $id);

            $result [] = [
                "label" => $object->name,
                "route" => '/objects/'.$object->type.'/'.$object->getKey(),
            ];
        }

        return array_reverse($result);
    }
    
    /**
     * Возвращает массив id всех родительских элементов
     * включая объект запроса
     * @return array
     */
    public function allParentsIds(){

        $result = collect();

        $result [] = $this->id;

        $parent = $this->parent;

        if ($parent) {
            $result[] = $parent->allParentsIds(); 
        }

        return $result->flatten();

    }


    /**
     * Возвращает массив id всех дочерних элементов
     * включая объект запроса
     * @return array
     */
    public function allChildren(){

        $result = collect();

        $result [] = $this->id;

        $children = $this->children;

        if (count($children) > 0) {
            foreach ($children as $child) {
                $result[] = $child->allChildren();   
            }
        }

        return $result->flatten();

    }
}
