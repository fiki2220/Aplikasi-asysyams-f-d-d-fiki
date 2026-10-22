<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ⭐ Security Headers untuk Best Practices Lighthouse
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // ⭐ Cache Headers untuk Performance Lighthouse (asset statis)
        $middleware->append(\App\Http\Middleware\CacheHeaders::class);

        // Alias yang sudah ada
        $middleware->alias([
            'is_active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'permission' => \App\Http\Middleware\EnsureUserHasPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 403 || ! $request->is('admin*')) {
                return null;
            }

            $user = $request->user();

            if (! $user) {
                return redirect()->route('login');
            }

            if ($user->role === 'student') {
                return redirect()->route('dashboard');
            }

            $fallback = $user->getFirstAllowedFilamentRoute();

            if ($fallback) {
                return redirect()->to($fallback);
            }

            return redirect()->route('access.denied');
        });
    })->create();