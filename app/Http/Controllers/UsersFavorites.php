<?php

namespace App\Http\Controllers;

use App\Models\BasisObject;
use App\Models\User;
use App\Models\UserFavorites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersFavorites extends Controller
{
        
    /**
     * 
     *
     * @param  BasisObject $object
     * @return array
     */
    public function toggleFavorites(BasisObject $object){
        
        $user = Auth::user();

        // todo permissions

        $favorites = UserFavorites::firstOrNew([
            "user_id" => $user->getKey(),
            "object_id" => $object->getKey()
        ]);

        if ($favorites->exists) {
            $favorites->delete();

            return [
                "message" => "Удалено из избранного",
                "favorites" => false,
            ];
        }

        $favorites->save();

        return [
            "message" => "Добавлено в избранное",
            "favorites" => true
        ];
    }
  
}
