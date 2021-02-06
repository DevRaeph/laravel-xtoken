<?php

namespace DevStorm\JWTToken;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use DevStorm\JWTToken\Http\Middleware\VerifyJWTToken;

class JWTTokenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        //$this->app->make('DevStorm\Response\Response');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('verifyJwt', VerifyJWTToken::class);
    }
}
