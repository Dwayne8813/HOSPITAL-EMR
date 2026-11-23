<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Console\Commands\CreateApiKeyCommand;
use App\Http\Middleware\AuthenticateApi;
use App\Http\Middleware\VerifyWebhookSignature;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'installer.check' => \App\Http\Middleware\InstallerCheck::class,
            'ensure.active' => \App\Http\Middleware\EnsureActive::class,
            'api.auth' => AuthenticateApi::class,
            'webhook.signature' => VerifyWebhookSignature::class,
        ]);
    })
    ->withCommands([
        CreateApiKeyCommand::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
