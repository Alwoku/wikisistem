<?php

use App\Http\Controllers\Feedbacks;
use App\Http\Controllers\Files;
use App\Http\Controllers\Folders;
use App\Http\Controllers\Index;
use App\Http\Controllers\Notes;
use App\Http\Controllers\Objects;
use App\Http\Controllers\Settings;
use App\Http\Controllers\SuggestedChangesObjects;
use App\Http\Controllers\Users;
use App\Http\Controllers\UsersFavorites;
use App\Http\Middleware\isActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("postLogin", [Index::class, "postLogin"])->name("postLogin");
Route::post("logout", [Index::class, "logout"])->name("logout");

Route::post("checked-style",[Index::class, "checkedUserStyle"])->name("checked-style");

Route::post("toggle-style-user", [Index::class, "toggleStyle"])->name("toggle-style-user");
Route::get("index", [Index::class, "index"])->name("index");


Route::get("user-read-info", [Users::class, "userReadInfo"])->name("user-read-info");
Route::post("profile/{user}", [Users::class, "profile"])->name("profile");
Route::post("panel-pin", [Users::class, "panelPin"])->name("panel-pin");
Route::get("ckeked-login", [Index::class, "checkedLogin"])->name("checkedLogin");
Route::get("footer-info", [Index::class, "footerInfo"])->name("footer-info");

Route::post("feedback-save", [Feedbacks::class, "save"])->name("feedback-save");

Route::get("reject-suggestion/{suggestion}", [SuggestedChangesObjects::class, "rejectSuggestion"])->name("reject-suggestion");

Route::get("suggestion-force-delete/{suggestion}", [SuggestedChangesObjects::class, "suggestionForceDelete"])->name("suggestion-force-delete");

Route::group([
    
    "as" => "settings.",
], function (){
    Route::get("history-of-changes", [Settings::class, "historyOfChanges"])->name("history-of-changes");
    Route::post("update-history-of-changes/{history?}", [Settings::class, "updateHistoryOfChanges"])->name("update-history-of-changes");

    Route::get("is-admin", [Users::class, "isAdmin"])->name("is-admin");
    Route::get("users", [Users::class, "users"])->name("users");
    Route::get("import-users", [Users::class, "importUsers"])->name("import-users");
    Route::post("update-profile", [Users::class, "updateProfile"])->name("update-profile");
    Route::post("search-users", [Users::class, "searchUsers"])->name("search-users");

    
    Route::get("expansion-files", [Files::class, "expansionFiles"])->name("expansion-files");
    Route::post("save/expansion-files", [Files::class, "saveExpansion"])->name("save-expansion-files");
    Route::post("delete/expansions-files", [Files::class, "deleteExpansion"])->name("delete-expansions-files");


    Route::get("groups", [Settings::class, "groups"])->name("groups");
    Route::post("update-group", [Settings::class, "updateGroup"])->name("update-group");
    Route::post("delete-group", [Settings::class, "deleteGroup"])->name("delete-group");

    Route::get("deleted-objects", [Objects::class, "deletedObjects"])->name("deleted-objects");

    Route::get("suggested-changes-for-admin", [SuggestedChangesObjects::class, "suggestedChangesForAdmin"])->name("suggested-changes-for-admin");


    Route::get("feedback", [Feedbacks::class, "index"])->name("feedback");
    Route::get("feedback-delete/{feedback}", [Feedbacks::class, "delete"])->name("feedback-delete");
});



Route::group([
    
    "as" => "objects.",
], function (){

    Route::get("breadcrumbs/{object?}", [Objects::class, "breadcrumbs"])->name("breadcrumbs");

    Route::get("note/{note?}", [Notes::class, "note"])->name("note");
    Route::post("note/{note?}", [Notes::class, "save"]);
    Route::get("versions/{note}", [Notes::class, "versions"])->name("versions");


    Route::get("toggle-favorites/{object}", [UsersFavorites::class, "toggleFavorites"])->name("toggle-favorites");
    Route::get("user-favorites", [Objects::class, "userFavorites"])->name("user-favorites");
     

    Route::get("folder/{folder?}", [Folders::class, "folder"])->name("folder");
    Route::post("folder/{folder?}", [Folders::class, "save"]);
    Route::post("save-condition-folders/{folder}", [Folders::class, "saveConditionFolders"])->name("save-condition-folders");

    Route::get("free-objects", [Objects::class, "freeObjects"])->name("free-objects");

    Route::get("updete-object/{object?}", [Objects::class, "updateObject"])->name("updete-object");
    Route::post("delete-object/{object}", [Objects::class, "deleteObject"])->name("delete-object");

    Route::post("force-delete", [Objects::class, "forceDelete"])->name("force-delete");
    Route::post("restoring-object", [Objects::class, "restoringObject"])->name("restoring-object");

    Route::post("global-finder", [Objects::class, "globalFinder"])->name("global-finder");


    Route::get("check-out-today-suggestion/{note}", [SuggestedChangesObjects::class, "checkoutTodaySuggestion"])->name("check-out-today-suggestion");
    Route::post("save-suggested-changes/{note}", [SuggestedChangesObjects::class, "updateSuggestedChanges"])->name("save-suggested-changes");
    Route::get("suggested-changes-for-author", [SuggestedChangesObjects::class, "suggestedChangesForAuthor"])->name("suggested-changes-for-author");


});