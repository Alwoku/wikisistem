<?php

namespace App\Http\Controllers;

use App\Basis;
use App\Models\BasisObject;
use App\Models\CacheOpenFolders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Folders extends Controller
{
     /**
     * Возвращает данные папки
     *
     * @param  BasisObject $folder
     * @return array
     */
    public function folder(BasisObject $folder){
        
        $user = Auth::user();

        // todo здесь должна быть проверка прав доступа

        if ($folder->exists && $folder->type !== 'folder') {
            abort(404);
        }
        
        $folder->load("children", "parent");

        $childrenIds = $folder->allChildren();

        $query = BasisObject::query();

        $query->where("type", "folder");

        if (count($childrenIds) > 1) {
            $query->whereNotIn("id", $childrenIds);
        }

        $folders = $query->get();

        $folder->favorites = $folder->usersFavorites()->where("user_id", $user->getKey())->exists();


        return [
            "folder" => $folder,
            "folders" => Basis::vueSelect($folders, "name", true)
        ];
    }
    
    /**
     * Сохранение изменений 
     *
     * @return array
     */
    public function save(BasisObject $folder) {
        
        $user = Auth::user();

        // todo permissions


        if (!request('name')) {
            return [
                "error" => "Поле название обязательно для заполнения"
            ];
        }
        // todo здесь должна быть проверка прав доступа

        $folder->fill([
            "name" => request('name'),
            "parent_id" => request('parent_id'),
        ]);

        // проверяем существование статьи
        if (!$folder->exists) {
            // если статья не существует заполняем поле автора и тип объекта
            $folder->fill([
                "type" => "folder",
                "user_id" => $user->getKey()
            ]);
        }

        $folder->save();

        return [
            "folder" => $folder
        ];
    }
    
    /**
     * Сохраняет состояние папки
     *
     * @return void
     */
    public function saveConditionFolders(BasisObject $folder){
        
        $user = Auth::user();

        if ($folder->type != "folder") {
            return;
        }

        $cacheFolder = CacheOpenFolders::firstOrNew([
                "user_id" => $user->getKey(),
                "folder_id" => $folder->getKey(), 
            ]);
        
        $cacheFolder->condition = request("condition");

        $cacheFolder->save();

    }
}
