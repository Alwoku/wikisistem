<?php

namespace App\Http\Controllers;

use App\Basis;
use App\Models\Group;
use App\Models\HistoryOfChanges;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class Index extends Controller
{
    /**
     * Главная страница
     *
     */
    public function index(){

        $user = Auth::user();

        $group = Group::whereRelation("users", "user_id", $user->getKey())
                ->whereNotNull("default_index")
                ->first();

        if ($group) {
                
            return [
                "idNote" => $group->default_index,
            ];
        }
        return [
            "idNote" => false,
        ];
    }
    
   
    /**
     * Обработка входа
     */
    public function postLogin(Request $request)
    {

        $user = User::where("email", request("email"))
            ->orWhere("name", request("email"))
            ->first();

        // такого пользователя нет в принципе
        if ($user === null) {
            return  ["errors" => "Неверный пользователь"];
        }

        try {
                Auth::login($user);
                session()->regenerate();

                return  [
                    "result" => true,
                    "messages" => "Вы успешно вошли",
                    "user" => Auth::user()
                ];

            return ["errors" => "Неправильные данные"];

        } catch (Exception $exception) {
            $message = $exception->getMessage();

            return  ["errors" => "Ошибка при входе : {$message}"];
        }
    }

    public function logout()
    {

        Auth::logout();
        session()->invalidate();

        return ["messages","Вы успешно вышли"];
    }

    
    /**
     * checkedUserStyle
     *
     * @return boolean
     */
    public function checkedUserStyle(){
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->dark_style;
    }
     
    /**
     * Переключает стиль сайта
     *
     * @return void
     */
    public function toggleStyle(){

        $user = Auth::user();

        $user->dark_style = !$user->dark_style;

        $user->save();

        return $user->dark_style;
    }
    
    /**
     * Проверка авторизации пользователя
     *
     * @return bool
     */
    public function checkedLogin(){

        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->is_active) {
                
                Auth::logout();
                session()->invalidate();

                return [
                    "result" => false,
                    "message" => "Ваша учетная запись не активна"
                ];
            }

            return [
                "user" => $user->only(config("basis.baseDataUser")),
                "result" =>true,
                "modalNotification" => $user->modalNotification ? $user->modalNotification->read : false,
            ];
        }

        return [
            "result" => false,
        ];
    }
    
    
    /**
     * информация для футера сайта
     *
     * @return array
     */
    public function footerInfo(){
        
        $user = Auth::user();

        $date = new Carbon(config("basis.version.date"));

        $info = HistoryOfChanges::where("active", true)->first();

        // если авторизованный пользователь не является админом
        // то скрываем информацию
        if(!$user->is_admin) $info->makeHidden(["admin_info"]);

        return [
            "versionSite" => config("basis.version.version"),
            "versionLaravel" => App::version(),
            "versionsPhp" => phpversion(),
            "date" => [
                "year" => date("Y"),
                "from" => $date->format("d.m.Y"),
                "diffForHumans" => $date->diffForHumans()
            ],
            "info" => $info,
            "modalNotification" => $user->modalNotification ? $user->modalNotification->read : true,
        ];
    }

}
