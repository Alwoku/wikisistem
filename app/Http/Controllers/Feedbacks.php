<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Feedbacks extends Controller
{    
    
    /**
     * Страница обратной связи в настройках
     *
     */
    public function index(){
        
        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }

        $feedbacks = Feedback::with("user")
            ->orderBy("created_at", "DESC")
            ->get();

        return [
            "feedbacks" => $feedbacks
        ];

    }

    /**
     * Сохранение обратной связи от пользователей
     *
     * @param  mixed $feedback
     * @return void
     */
    public function save(Feedback $feedback){

        Validator::make(request()->all(), [
            "title"=>'required|max:255',
            "text"=>'required'
        ])->validate();

        $user = Auth::user();

        $feedback->forceFill([
            "user_id" => $user->getKey(),
            "title" => request("title"),
            "text" => request("text")
        ]);

        $feedback->save();

        return [
            "result" => true
        ];
    }
    
    /**
     * delete
     *
     * @param  Feedback $feedback
     */
    public function delete(Feedback $feedback){
        
        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }

        $feedback->delete();

        
    }
}
