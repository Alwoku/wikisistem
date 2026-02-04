<?php

namespace App\Http\Controllers;

use App\Basis;
use App\Console\Commands\UpdateModalNotification;
use App\Models\BasisObject;
use App\Models\ExpansionFile;
use App\Models\Group;
use App\Models\HistoryOfChanges;
use App\Models\ModalNotification;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class Settings extends Controller
{
     
    /**
     * Страница со списком групп
     *
     */
    public function groups(){
        
        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403); 
        }

        $groups = Group::all()->load('users');
        

        for ($i=0; $i < $groups->count(); $i++) { 
            $groups[$i]['usersList'] = $groups[$i]['users']->select('user_id');
        }
        
        $options = [
            "users" => Basis::vueSelect(User::where("is_active", true)->orderBy('name')->get()),
            "pages" => Basis::vueSelect(BasisObject::where("type", "note")->get())
        ];


        return [
            "groups" => $groups->toArray(),
            "options" => $options,
            // todo тут должен быть список с объектами  и правами жоступа
        ];


    }
    
    /**
     * Создание новой группы
     * сохрание измененний в существующей группе
     *
     * @return array
     */
    public function updateGroup(Request $request){
 
        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }

        if (!$request->name) {
            return [
                "result" => false,
                "error" => trans("validation.required", ["attribute" => "Название"])
            ];
        }
        // забираем группу либо создаем новую
        $group = Group::firstOrNew([
            "id" => $request->id,
        ]);

        if (Group::where("name", $request->name)
            ->whereNot("id", $group->getKey())
            ->exists()) {
            return [
                "error" => "Такое имя уже существует"
            ];
        }

        if(!$group->exists){
            $group->author = $user->getKey();
        }
        
        // заполняем значения
        $group->fill([
            "name" => $request->name,
            "default_index" => $request->default_index,
            "user_id" => $user->getKey()
        ]); 
        
        // сохраняем
        $group->save();
        
        // обновляем отношения
        $group->syncMany("users", $request->usersList);

        return [
            "result" => true,
            "group" => $group
        ];
    }
        
    /**
     * удаление группы
     *
     * @param  Request $request
     * @return array
     */
    public function deleteGroup(Request $request){
        
        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }

        // проверяем существование группы

        $group = Group::whereKey($request->id)->first();

        if (!$group) {
            return [
                "result" => false,
                "message" => "Такой группы не существует"
            ];
        }

        $group->delete();

        return [
            "result" => true,
            "message" => "Группа удалена"
        ];
    }
 
    /**
     * страница с информацией об изменениях на сайте
     *
     * @return array
     */
    public function historyOfChanges(){
        
        $user = Auth::user();

        if(!$user->is_admin){
            abort(403);
        }

        $history = HistoryOfChanges::orderBy("created_at", "DESC")->get();

        return ["history" => $history];
    }
    
    /**
     * обновление информации об изменениях на сайте
     *
     * @param HistoryOfChanges $history
     * @return array
     */
    public function updateHistoryOfChanges(HistoryOfChanges $history){
        
        $user = Auth::user();

        if(!$user->is_admin){
            abort(403);
        }

        if(config("basis.version.version") != request('version')){
            return [
                "error" => "Версии сайта в конфигурации и истории не совпадают. Обновите версию"
            ];
        }

        $history->version = request('version');
        $history->info = strval(request('info'));

        if (request('admin_info')) {
            $history->admin_info = request('admin_info');
        }

        $history->active = true;

        HistoryOfChanges::where("active", true)->update([
                "active" => false
            ]);

        $history->save();

        Artisan::call(UpdateModalNotification::class);

        return [
            "message" => trans("save"),
            "history" => HistoryOfChanges::orderBy("created_at", "DESC")->get(),
        ];
    }
}
