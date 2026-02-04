<?php

namespace App\Providers;

use App\Models\Content;
use App\Observers\ContentObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Content::observe(ContentObserver::class);

        View::creator("base", function ($view) {
            $view->with("user", Auth::user());
            $view->with("version", new Carbon(config("basis.version.date")));

            $view->with("laravelVersion", App::version());
        });
    }
}
