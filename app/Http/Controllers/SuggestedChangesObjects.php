<?php

namespace App\Http\Controllers;

use App\Models\BasisObject;
use App\Models\SuggestedChanges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Completion\Suggestion;

class SuggestedChangesObjects extends Controller
{
        
    /**
     * 
     *
     * @param  BasisObject $note
     * @return array
     */
    public function checkoutTodaySuggestion(BasisObject $note){
        // todo permission
        
        $user = Auth::user();

        $suggestion = SuggestedChanges::where("user_id", $user->getKey())
                ->where("object_id", $note->getKey())
                ->whereToday("created_at")
                ->first();

        return [
            "suggestion" => $suggestion
        ];
    }
    
    /**
     * Предложеные изменения в статье
     *
     * @param  SuggestedChanges $note
     * @return array
     */
    public function updateSuggestedChanges(SuggestedChanges $suggestion){
        
        // todo permission
        $user = Auth::user();

        if ($suggestion->exists) {

            $note = $suggestion->object;

            $note->text = $note->content ? $note->content->content : "";

            if ($note->parent_id === request('parent_id') &&
                $note->name === request('name') &&
                $note->text == request('text') 
            ) {
                return "error";
            }

        }

        $suggestion->fill([
            "object_id" => request('object_id'),
            "user_id" => $user->getKey(),
            "parent_id" => request('parent_id'),
            "name" => request('name'),
            "text" => request('text'),
        ]);


        if (request('comment')) $suggestion->comment = request('comment');

        $suggestion->save();

        return [
            "message" => "Отправлено"
        ];
    }

    
    /**
     * suggestedChangesForAuthor
     *
     * @return array
     */
    public function suggestedChangesForAuthor(){

        // todo permission

        $user = Auth::user();


        $suggested = $user->suggestedChanges;

        $suggested->load("object.content", "object.parent", "parentObject", "user");
        
        $rejectedSuggested = SuggestedChanges::onlyTrashed()
            ->where("user_id", $user->getKey())
            ->get();

        $rejectedSuggested->load("object.content", "object.parent", "parentObject", "user");

        return [
            "rejected" => $rejectedSuggested,
            "suggested" => $suggested,
        ];

    }

    
    /**
     * suggestedChangesForAdmin
     *
     * @return array
     */
    public function suggestedChangesForAdmin(){

        // todo permission

        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }
        
        $suggested = SuggestedChanges::all();

        $suggested->load("object.content", "object.parent", "parentObject", "user");

        $rejectedSuggested = SuggestedChanges::onlyTrashed()->get();
        $rejectedSuggested->load("object.content", "object.parent", "parentObject", "user");

        return [
            "rejected" => $rejectedSuggested,
            "suggested" => $suggested
        ];
    }
    
    /**
     * rejectSuggestion
     *
     * @param  SuggestedChanges $suggestion
     * @return void
     */
    public function rejectSuggestion(SuggestedChanges $suggestion){
        
        $user = Auth::user();

        if(!$user->is_admin && $suggestion->user_id != $user->getKey()){
            abort(403);
        }

        $suggestion->delete();

    }
    
    /**
     * suggestionForceDelete
     *
     * @param  mixed $suggestion
     * @return void
     */
    public function suggestionForceDelete(SuggestedChanges $suggestion){
        
        $user = Auth::user();

        if(!$user->is_admin && $suggestion->user_id != $user->getKey()){
            abort(403);
        }

        $suggestion->forceDelete();
    }
}
