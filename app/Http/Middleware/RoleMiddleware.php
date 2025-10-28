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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek jika user tidak login atau role-nya tidak termasuk dalam $roles yang diizinkan
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            // Jika tidak diizinkan, kembalikan ke halaman 403 (Forbidden)
            abort(403);
        }

        return $next($request);
    }
}