<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Route::prefix('api')
        // ->middleware('api')
        // ->namespace($this->app->getNamespace().'Http\Controllers')
        // ->group(base_path('routes/api.php'));
    }
}
