<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // 🔥 LOGIKA REDIRECT BERDASARKAN ROLE
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } 
        
        if ($user->role === 'cashier') {
            return redirect()->route('cashier.dashboard');
        }

        // TAMBAHKAN INI: Redirect khusus untuk kru Dapur
        if ($user->role === 'kitchen') {
            return redirect()->route('kitchen.index');
        }

        // Jika user tidak punya role yang dikenal, logout dan cegah akses
        Auth::logout();
        abort(403, 'Akses ditolak. Role tidak terdaftar.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}