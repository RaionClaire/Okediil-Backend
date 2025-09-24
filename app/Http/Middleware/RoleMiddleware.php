<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:superadmin,admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $status = null;

        $user = $request->user();
        if (!$user) {
            $status = response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$status && !empty($roles)) {
            $userRole = strtolower($user->role ?? '');
            $allowed = collect($roles)->map(fn($r) => strtolower($r))->contains($userRole);
            if (!$allowed) {
                $status = response()->json(['message' => 'Forbidden: insufficient role'], 403);
            }
        }

        return $status ?: $next($request);
    }
}
