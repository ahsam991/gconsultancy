<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('role:admin') or ->middleware('role:staff,manager')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = $user->role?->name;

        // Admin bypasses all role checks.
        if ($userRole === 'admin') {
            return $next($request);
        }

        $allowed = [];
        foreach ($roles as $group) {
            foreach (preg_split('/[|,]/', $group) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $allowed[] = $part;
                }
            }
        }

        if (! in_array($userRole, $allowed, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
