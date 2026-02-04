<?php

namespace App\Http\Controllers;

use App\Basis;
use App\Models\BasisObject;
use App\Models\Group;
use App\Models\SuggestedChanges;
use App\Models\UserFavorites;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Objects extends Controller
{    
    /**
     * Возвращает массив для хлебных крошек
     *
     * @param  BasisObject $object
     * @return array
     */
    public function breadcrumbs(BasisObject $object){

        if (!$object->exists) {
            return [];
        }

        return $object->createdBreadcrumbs();
    }
    
    /**
     * Возвращает дерево объектов
     *
     * @return array
     */
    public function freeObjects(){

        $objects = BasisObject::whereNull("parent_id")->get();

        $free = $this->traversingTree($objects);

        return $free;
    }
    
    /**
     * Обходит и формирует дерево объектов
     *
     * @param  mixed $objects
     * @return array
     */
    public function traversingTree($objects){
        
        // Забираем пользователя
        $user = Auth::user();

        // проверяем его существование 
        if(!$user){
            return [];
        }

        $result = [];

        foreach ($objects as  $object) {

            // создаем начальный массив данных для дерева
            $data = [
                "id" => $object->getKey(),
                "label" => $object->name,
                "type" => $object->type,
                "route" => '/objects/'.$object->type."/".$object->getKey(),
            ];

            // если объект является папкой,
            if ($object->type === 'folder') {
                // проверяем открыта ли она
                $openFolder = $user->openFolders()
                                ->where("folder_id", $object->getKey())
                                ->first();

                $data['open'] = $openFolder ? $openFolder->condition : false;

                // проеряем существуют ли дочерние элементы
                if ($object->children()->exists()) {
                    $data['children'] =$this->traversingTree($object->children);
                }
            }

            
            //добавляем данные в результирующий массив 
            array_push($result, $data);
        }
        
        return $result;
    }
    
    /**
     * Страница с удаленными папками/статьями
     * возвращает список всех удаленных объектов
     *
     * @return array
     */
    public function deletedObjects(){
        
        // проверяем что пользователь является админом

        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }

        // забираем все удаленные объекты
        $objects = BasisObject::onlyTrashed()
                    ->orderBy("deleted_at", "DESC")
                    // ->with()
                    ->get()
                    ->load(["content", "user", "parent" => function ($query)  {
                        $query->withTrashed();
                    }]);

        $folders = BasisObject::where("type", "folder")->get();
        return[
            "objects" => $objects,
            "folders" => Basis::vueSelect($folders, "name", true)
        ];
    }
    
    /**
     * Удаление папки статьи
     *
     * @return boolean
     */
    public function deleteObject(BasisObject $object){
        
        // todo permissions
        // если объект является папкой
        if ($object->type == 'folder') {

            // проверяем флаг удаления всей верти
            if (request('deleteBranch')) {
                
                // забираем всю ветку объектов
                $allIds = $object->allChildren();
                $objects = BasisObject::whereIn("id", $allIds)->get();

                // todo permissions
                // обходим массив объектов и удаляем их
                foreach ($objects as $element) {
                    $element->delete();
                }

                return [
                    "result" => true
                ];
            }

            // обновляем родителя у дочерних объектов 
            BasisObject::where("parent_id", $object->getKey())
                    ->update([
                        "parent_id" => request("parent_id")
                    ]);
        }else{
            
            // меняем стартовую  страницу у групп на null 
            Group::where("default_index", $object->getKey())
                    ->update([
                        "default_index" => null
                    ]);

        }

        // удаляем объект
        $object->delete();

        return [
            "result" => true
        ];
    }
        
    /**
     * Окончательное удаление из бд объекта
     *
     * @return array
     */
    public function forceDelete(){
        
        // проверяем что пользователь админ
        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }
        
        // удаляем объект из бд
        $object = BasisObject::onlyTrashed()->whereKey(request('objectId'))->first();

        $object->forceDelete();
        
        return [
            "result" => true
        ];
    }
    
    /**
     * восставновление удаленного объекта 
     *
     * @return array
     */
    public function restoringObject(){
        
        // проверяем что пользователь админ
        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }

        // восстанавливаем объект
        BasisObject::withTrashed()
                ->whereKey(request('id'))
                ->restore();


        // если стоит флаг смены родителя
        if (request("update_parent")) { 
            // обновляем id родителя
            BasisObject::whereKey(request('id'))
                ->update(["parent_id" => request("parentId")]);
        }
        return [
            "result" => true
        ];

    }
    
    /**
     * Глобальный поиск по папкам/статьям
     *
     * @return array
     */
    public function  globalFinder() {
        
        // todo permissions

        $user = Auth::user();

        $search = strval(request("search"));

        $objects = BasisObject::where("name", "LIKE", "%{$search}%")
                ->orWhereHas("content", function (Builder $query) use ($search){
                    $query->where("content", "LIKE", "%{$search}%" );
                })->get();
        
        
        return $this->formationResult($objects, $search);
    }

      
    /**
     * Составляет результирующий массив
     * найденных по запросу объектов
     *
     * @param  array $objects
     * @param  string $search
     * @return array
     */
    public function formationResult($objects, $search){
        $result = [];


        /** @var BasisObjects $note */
        foreach ($objects as $object) {


            $row = [
                "id" => $object->getKey(),
                "name" => $object->name,
                "type" => $object->type,
                "search" => ""
            ];


            if($object->type === "folder" || $object->content === null){

                $result[] = $row;
                continue;
            }

            $object->load("content");
            
            $position = mb_stripos($object["content"]->content, $search);

            // нам нужно найти в содержимом найденный текст

            $substr = null;

            // сколько символов вокруг поисковой фразы брать
            $surroundChars = 50;

            // если в содержимом записи есть подстрока
            if ($position !== false) {
                // начальная позиция (минимум 0)
                $start = max(0, $position - $surroundChars);

                // разница между позицией совпадения и позицией старта подстроки
                $startLength = $position - $start;

                // общая длина контекста
                $length = $startLength + Str::length($search) + $surroundChars;

                $substr = mb_substr($object['content']->content, $start, $length);

                // заменяем поисковую фразу на жирный текст
                $pregQuote = preg_quote($search);
                $substr = preg_replace("#{$pregQuote}#ui", '<b>$0</b>', $substr);
            }

            $row["search"] = $substr;

            $result[] = $row;
        }
        return $result;
    }

      
    /**
     * Список избранного пользователя
     *
     * @param  User $user
     * @return array
     */
    public function userFavorites(){
        
        $user = Auth::user();

        // todo permissions

        $objects = BasisObject::whereRelation("usersFavorites", "user_id", $user->getKey())
                ->get();
    
        

        return[ "objects" => $this->traversingTree($objects)];
    }
}
