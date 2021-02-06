<?php

namespace DevRaeph\XToken;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use DevRaeph\XToken\Http\Middleware\VerifyJWTToken;

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
        $this->app->bind('tokenizer', function($app) {
            return new Tokenizer();
        });
        $this->app->bind('tokenizerclaim', function($app) {
            return new TokenizerClaim();
        });
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

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'xtoken');
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('xtoken.php'),
            ], 'config');

        }

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('xToken', VerifyJWTToken::class);
    }
}
