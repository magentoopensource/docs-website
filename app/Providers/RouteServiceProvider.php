<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * No middleware group is applied here: routes/web.php assigns "docs" (no session, cacheable)
     * to the GET docs routes and "web" (session, CSRF) to the webhook POST.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([], base_path('routes/web.php'));
    }
}
