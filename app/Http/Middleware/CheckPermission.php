<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permiso)
    {
        if (!Auth::user() || !Auth::user()->permisos()->contains($permiso)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return $next($request);
    }
}
