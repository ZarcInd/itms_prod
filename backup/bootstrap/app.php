<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.cookie.auth' => \App\Http\Middleware\AdminAuthenticate::class,
            'api.loglatency' => \App\Http\Middleware\LogApiLatency::class,
	     'auth' => \App\Http\Middleware\Authenticate::class,
            'admin.2fa.complete' => \App\Http\Middleware\EnsureAdminTwoFactorAuthenticationIsComplete::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Schedule the command to process pending exports every 5 minutes
        $schedule->command('exports:process-pending')->everyFiveMinutes();
        
        // Or schedule daily processing for current date
        $schedule->command('exports:process-pending --date=' . date('Y-m-d'))->daily();
    })

    ->create();
