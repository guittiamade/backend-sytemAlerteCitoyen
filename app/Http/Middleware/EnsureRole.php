<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $roleName = optional($user?->profile)->nom;
        if (!$user || ($roles && !in_array($roleName, $roles, true))) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }
        return $next($request);
    }
}


