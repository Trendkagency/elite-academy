<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\EnsureAdminAccountExists::class,
        ]);
        $middleware->alias([
            'role.student' => \App\Http\Middleware\EnsureStudentRole::class,
            'role.teacher' => \App\Http\Middleware\EnsureTeacherRole::class,
            'role.parent' => \App\Http\Middleware\EnsureParentRole::class,
            'role.admin' => \App\Http\Middleware\EnsureAdminRole::class,
            'permission' => \App\Http\Middleware\EnsureHasPermission::class,
            'redirect.teacher' => \App\Http\Middleware\RedirectTeacherToPortal::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->is('ajax/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\Illuminate\Database\UniqueConstraintViolationException $e, Request $request) {
            if ($request->hasHeader('x-livewire') || $request->is('livewire/*') || $request->is('admin*')) {
                \Filament\Notifications\Notification::make()
                    ->title(__('Duplicate Record'))
                    ->body(__('A record with this unique information (such as slug, name, or email) already exists. Please choose a different value.'))
                    ->danger()
                    ->persistent()
                    ->send();
            }

            if ($request->is('api/*') || $request->is('ajax/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('A record with this unique value already exists.'),
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e, Request $request) {
            $errorCode = $e->errorInfo[1] ?? 0;

            // MySQL Error 1451: Foreign key constraint fails on delete
            if ($errorCode == 1451) {
                if ($request->hasHeader('x-livewire') || $request->is('livewire/*') || $request->is('admin*')) {
                    \Filament\Notifications\Notification::make()
                        ->title(__('Cannot Delete Record'))
                        ->body(__('This item cannot be removed because other active data depends on it (e.g. active courses, enrollments, or profiles).'))
                        ->warning()
                        ->persistent()
                        ->send();
                }

                if ($request->is('api/*') || $request->is('ajax/*') || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Cannot delete this item because related records depend on it.'),
                    ], 422);
                }

                return back()->with('error', __('Cannot delete this item because related records depend on it.'));
            }

            // MySQL Error 1062: Duplicate entry
            if ($errorCode == 1062) {
                if ($request->hasHeader('x-livewire') || $request->is('livewire/*') || $request->is('admin*')) {
                    \Filament\Notifications\Notification::make()
                        ->title(__('Duplicate Entry'))
                        ->body(__('A duplicate entry was detected. Please ensure all unique fields have distinct values.'))
                        ->danger()
                        ->persistent()
                        ->send();
                }

                if ($request->is('api/*') || $request->is('ajax/*') || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Duplicate record detected.'),
                    ], 422);
                }

                return back()->withInput()->with('error', __('A duplicate record already exists.'));
            }
        });
    })->create();
