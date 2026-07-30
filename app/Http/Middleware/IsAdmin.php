<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/absen');
        }

        $user = auth()->user();

        // Izinkan semua varian Super Admin
        $isAdmin = $user->username === 'admin'
            || in_array($user->is_admin, ['admin', 'superadmin', 'Super Admin'])
            || $user->hasRole('admin')
            || $user->hasRole('Super Admin')
            || $user->hasRole('superadmin');

        if (!$isAdmin) {
            return redirect('/absen');
        }

        return $next($request);
    }
}
