<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\AuditLog; 

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses Login (Store).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Proses Login (Cek Password dll)
        $request->authenticate();

        // 2. Regenerasi Session (Keamanan)
        $request->session()->regenerate();

        // 3. Ambil data user yang login
        $user = Auth::user();

        // 4. CATAT LOG AKTIVITAS (FITUR LENGKAP KEMBALI)
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'LOGIN',
            'details' => 'Login ke sistem',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        // 5. Redirect ke Dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Proses Logout (Destroy).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Catat Log Logout (FITUR LENGKAP KEMBALI)
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGOUT',
                'details' => 'Logout dari sistem',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        }

        // 2. Logout User
        Auth::guard('web')->logout();

        // 3. Bersihkan Session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 4. Lempar balik ke halaman login
        // [PERUBAHAN DISINI] Mengarahkan ke '/login' bukan '/'
        return redirect('/login');
    }
}