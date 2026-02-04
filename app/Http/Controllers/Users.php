<?php

namespace App\Http\Controllers;

use App\Basis;
use App\Console\Commands\ImportUsers;
use App\Models\Group;
use App\Models\ModalNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class Users extends Controller
{
        
    /**
     * Страница со списком пользователей
     *
     */
    public function users(){

        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403); 
        }

        $users = User::select(config("basis.baseDataUser"))
                    ->get()
                    ->load("groups.group");
        return  $users->groupBy("is_active");
    }

    /**
     *  проверка пользователя на админа
     *  для промежуточной проверки во vue router
     * @return bool
     */
    public function isAdmin(){
        $user = Auth::user();

        return !$user ?: $user->is_admin ;
    }
    
    /**
     * Обновление данных пользователя
     *
     * @param  Request $request
     * @return array
     */
    public function updateProfile(Request $request){
        // проверяем является ли изначальный пользователль админом

        if (!$this->isAdmin()) {
            abort(403);
        }

        // проверка существования пользователя

        $user = User::whereKey($request->id)->first();

        if (!$user) {
            return [
                "result" => false,
                "message" => "Такого пользователя не существует"
            ];
        }

        // заполняем данные пользователя
        
        $user->fill([
            "basis_active" => $request->basis_active,
            "is_admin" => $request->is_admin
        ]);
        $user->save();


        //  обновляем отношения

        $user->syncMany("groups", $request->groupsList);

        // todo: добавить touch модели Group полей user_id updated_at

        return [
            "result" => true,
            "message" => "Сохранено"
        ];
    }

    /**
     * 
     * страница пользователя
     *
     * @param  User $user
     * @return array
     */
    public function profile(User $user)
    {
        $currentUser = Auth::user();

        if($user->getKey() != $currentUser->id &&
            !$currentUser->is_admin) {
            
            return[
                "error" => "403"
            ];
            
        }
        $user['groupsList'] = $user->groups()->select('group_id')->get();
        $config = config("basis.dataUser");
        array_push($config, "groupsList");

        return [
            "user" => $user->only($config),
            "currentUser" => $currentUser->only(config("basis.baseDataUser")),
            "groups" => Basis::vueSelect(Group::all()),
        ];
    }

     /**
     * импорт пользователей из императтора
     *
     */
    public function importUsers() {  
        
        // проверяем является ли изначальный пользователль админом

        if (!$this->isAdmin()) {
            abort(403);
        }

        $result = Artisan::call(ImportUsers::class);

        if (!$result === 0) {
            return [
                "error" => "Были ошибки"
            ];
        }

        return [
            "message" => "Успешно",
            "users" => $this->users()
        ];
    }
        
    /**
     * пооиск по пользователям 
     *
     * @return array
     */
    public function searchUsers(){
        
        // проверяем является ли изначальный пользователль админом

        if (!$this->isAdmin()) {
            abort(403);
        }
        $search = strval(request('search'));

        $users = User::select(config("basis.baseDataUser"))
                    ->where("name", "LIKE",  "%{$search}%")
                    ->get();

        return ["users" => $users];
    }
    
    /**
     * фиксация открытой панели меню
     *
     * @return void
     */
    public function panelPin(){
        
        $user = Auth::user();

        if ($user->panel_pin == request("menu")) {
            $user->panel_pin = null;
        }else{
            $user->panel_pin = request("menu");
        }
        
        $user->save();
    }
    
    /**
     * пользователь прочел информацию об изменениях на сайте
     *
     * @return void
     */
    public function userReadInfo(){

        $user = Auth::user();

        $notification = ModalNotification::firstOrNew([
            "user_id" => $user->getKey()
        ]);

        $notification->read = true;
        $notification->save();

    }
}
