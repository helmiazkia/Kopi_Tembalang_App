<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // cek user login
        if (!auth()->check()) {
            abort(401, 'Belum login');
        }

        $user = auth()->user();

        // cek apakah role user ada di daftar role
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}
