<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);
        $middleware->alias([
            'role.student'     => \App\Http\Middleware\EnsureStudentRole::class,
            'role.teacher'     => \App\Http\Middleware\EnsureTeacherRole::class,
            'role.parent'      => \App\Http\Middleware\EnsureParentRole::class,
            'role.admin'       => \App\Http\Middleware\EnsureAdminRole::class,
            'permission'       => \App\Http\Middleware\EnsureHasPermission::class,
            'redirect.teacher' => \App\Http\Middleware\RedirectTeacherToPortal::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
