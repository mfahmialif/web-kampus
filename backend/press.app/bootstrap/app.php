<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Auto-detect Environment File
|--------------------------------------------------------------------------
|
| Jika server punya env variable APP_ENV=production,
| maka Laravel akan load .env.production otomatis.
| Set APP_ENV=production di hosting panel / server config.
|
*/
$serverEnv = getenv('APP_ENV') ?: ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null);

$envFile = match (true) {
    $serverEnv === 'production'                             => '.env.production',
    file_exists(dirname(__DIR__) . '/.env.production')
        && !file_exists(dirname(__DIR__) . '/.env')         => '.env.production',
    default                                                 => '.env',
};

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->api(replace: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class => \App\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

$app->loadEnvironmentFrom($envFile);

/*
|--------------------------------------------------------------------------
| Override Public Path
|--------------------------------------------------------------------------
|
| Arahkan public_path() ke public_html
| Cocok untuk shared hosting
|
*/

$app->usePublicPath(realpath(base_path('../public_html')));

return $app;
