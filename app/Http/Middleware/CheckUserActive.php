<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckUserActive
{
    /**
     * Cek apakah user yang sedang login masih ada di database.
     * Jika sudah dihapus admin, paksa logout dan redirect ke login.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = User::find(Auth::id());

            if (!$user) {
                // User sudah dihapus dari database — paksa logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')->with('error', 'Akun Anda telah dihapus oleh administrator. Silakan hubungi admin.');
            }
        }

        return $next($request);
    }
}
