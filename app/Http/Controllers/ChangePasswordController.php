<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\AuditLog; 

class ChangePasswordController extends Controller
{
    /**
     * Tampilkan form ganti password.
     */
    public function edit()
    {
        return view('auth.change-password'); 
    }

    /**
     * Proses update password.
     */
    public function update(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ], [
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password_baru.min' => 'Password baru minimal 6 karakter.'
        ]);

        // 2. Cek Password Lama
        if (!Hash::check($request->password_lama, auth()->user()->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Password lama yang Anda masukkan salah.',
            ]);
        }

        // 3. Update Password
        // Kita update manual pakai Hash::make biar pasti aman
        auth()->user()->update([
            'password' => Hash::make($request->password_baru)
        ]);

        // 4. CATAT AUDIT LOG (PERBAIKAN: 'description' -> 'details')
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UBAH_PASSWORD',
            'details' => 'Pengguna mengubah password akun.', 
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        // 5. Kirim Notifikasi Sukses ke View
        return back()->with('success', 'Password berhasil diperbarui! Silakan ingat password baru Anda.');
    }
}