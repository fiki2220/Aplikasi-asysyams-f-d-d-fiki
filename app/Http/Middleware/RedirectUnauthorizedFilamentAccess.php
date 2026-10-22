<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectUnauthorizedFilamentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->role === 'superadmin') {
            return $next($request);
        }

        if ($user->role === 'student') {
            return redirect()->route('dashboard');
        }

        if ($user->role !== 'guru') {
            return redirect()->route('access.denied');
        }

        $routeName = $request->route()?->getName();

        if ($routeName === 'filament.admin.home' && ! $user->hasAccess('dashboard.view')) {
            return $this->redirectToAllowedPage($user, $request);
        }

        $permissions = $this->permissionsForRoute($routeName);

        if ($permissions === []) {
            return $next($request);
        }

        if ($user->hasAnyAccess($permissions)) {
            return $next($request);
        }

        return $this->redirectToAllowedPage($user, $request);
    }

    private function permissionsForRoute(?string $routeName): array
    {
        if (! $routeName || $routeName === 'filament.admin.home') {
            return [];
        }

        if (str_contains($routeName, '.resources.role-permissions.')) {
            return ['superadmin.only'];
        }

        if (str_contains($routeName, '.resources.site-settings.manage-spmb')) {
            return ['settings.update', 'settings.manage'];
        }

        $module = match (true) {
            str_contains($routeName, '.resources.candidates.') => 'spmb',
            str_contains($routeName, '.resources.class-groups.') => 'classes',
            str_contains($routeName, '.resources.semesters.') => 'semesters',
            str_contains($routeName, '.resources.meetings.') => 'meetings',
            str_contains($routeName, '.resources.assessments.') => 'assessments',
            str_contains($routeName, '.resources.evaluations.') => 'evaluations',
            str_contains($routeName, '.resources.grades.') => 'grades',
            str_contains($routeName, '.resources.payments.') => 'payments',
            str_contains($routeName, '.resources.posts.') => 'posts',
            str_contains($routeName, '.resources.site-settings.') => 'settings',
            str_contains($routeName, '.resources.rapor.') => 'reports',
            str_contains($routeName, '.resources.users.') => 'users',
            default => null,
        };

        if (! $module) {
            return [];
        }

        if ($module === 'reports') {
            return ['reports.view'];
        }

        $action = match (true) {
            str_contains($routeName, '.create') => 'create',
            str_contains($routeName, '.edit') => 'update',
            default => 'view',
        };

        return ["{$module}.{$action}", "{$module}.manage"];
    }

    private function redirectToAllowedPage($user, Request $request): Response
    {
        $fallback = $user->getFirstAllowedFilamentRoute();

        if ($fallback && $fallback !== $request->fullUrl()) {
            return redirect()->to($fallback);
        }

        return redirect()->route('access.denied');
    }
}
