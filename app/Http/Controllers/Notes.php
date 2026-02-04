<?php

namespace App\Http\Controllers;

use App\Basis;
use App\Models\BasisObject;
use App\Models\Content;
use App\Models\SuggestedChanges;
use App\Models\SuggestionChange;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Notes extends Controller
{    
    /**
     * Возвращает данные статьи
     *
     * @param  BasisObject $note
     * @return array
     */
    public function note(BasisObject $note){
        
        $user = Auth::user();

        // todo здесь должна быть проверка прав доступа

        if ($note->exists && $note->type !== 'note') {
            abort(404);
        }
        // todo нужно добавить загрузку тегов
        $note->load("content", "parent", "suggestedChanges.user");


        $note->text = $note->content ? $note->content->content : "";

        $folders = BasisObject::where("type", "folder")
                ->get();

        $note->favorites = $note->usersFavorites()->where("user_id", $user->getKey())->exists();

        return [
            "note" => $note,
            "folders" => Basis::vueSelect($folders, "name", true)
        ];
    }
    
    /**
     * Сохраняет данные статьи
     *
     * @param  BasisObject $note
     * @return array
     */
    public function save(BasisObject $note){

        // todo permission

        $user = Auth::user();

        if (!request('name')) {
            return [
                "error" => "Поле название обязательно для заполнения"
            ];
        }
        // todo здесь должна быть проверка прав доступа

        $note->fill([
            "name" => request('name'),
            "parent_id" => request('parent_id'),
        ]);

        // проверяем существование статьи
        if (!$note->exists) {
            // если статья не существует заполняем поле автора и тип объекта
            $note->fill([
                "type" => "note",
                "user_id" => $user->getKey()
            ]);
        }

        if (request('update_version')) {

            $note->version = request("version") == $note->version ?  $note->version+1 : request("version");

            // todo добавить оповещение в телеграмм
        }

        // сохраняем статью
        $note->save();

        // забираем модель контента
        $content = Content::firstOrNew([
            "object_id" => $note->getKey(),
        ]);
       
        // проверяем изменения
        if (!$content->exists || $content->content !== request("text")) {

            if ($content->exists ) {
                
                // сохраняем старую версию текста
                $version = Version::create([
                    "object_id" => $note->getKey(),
                    "version" => $note->version,
                    "content" => $content->content,
                    "user_id" => $user->getKey(),
                ]);

                $version->save();
            }

            // сохраняем измененный текст
            $content->content = request('text') ? strval(request('text')) : "";
            $content->save();

        }

        return [
            "note" => $note,
            "breadcrumbs" => $note->createdBreadcrumbs()
        ];
    }
       
    /**
     * Страница истории версий
     *
     * @param  BasisObject $note
     * @return void
     */
    public function versions(BasisObject $note){
        
        // todo permissions
        $user = Auth::user();

        if (!$user->is_admin || 
            $user->getKey() != $note->user_id) {
            abort(403);
        }

        // забираем все версии для данной статьи
        $versions = Version::where("object_id", $note->getKey())
            ->orderBy("created_at", "DESC")
            ->get()
            ->load("user");

        // забираем актуальную версию контента
        $note->load("content");

        return [
            "versions" => $versions,
            "note" => $note
        ];
    }

    

}
