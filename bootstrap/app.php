<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

// Define o ambiente padrão como 'local' se não estiver definido
$envName = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'local';

// Monta o nome do arquivo (ex: .env.local)
$envFile = '.env.' . $envName;
$envPath = dirname(__DIR__) . '/' . $envFile;

// Verifica se o arquivo específico existe. Se não, usa o .env padrão.
// A função is_file() é mais segura que file_exists() aqui.
$envToLoad = is_file($envPath) ? $envFile : '.env';

// Cria a aplicação
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })
    ->create();

// Carrega o arquivo escolhido ANTES de retornar a aplicação
$app->loadEnvironmentFrom($envToLoad);

return $app;