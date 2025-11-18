<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    error_log("🔥 ERROR before Handler: {$message} in {$file}:{$line}");
});
set_exception_handler(function ($e) {
    error_log("🔥 EXCEPTION before Handler: " . get_class($e) . " — " . $e->getMessage() . " — at " . $e->getFile() . ":" . $e->getLine());
});

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Sanctum middleware moderno
        $middleware->statefulApi();

        // Seus middlewares customizados
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {

            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            return null; // deixa o Laravel continuar a renderização normal
        });


    })
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
    ])
    ->create();
