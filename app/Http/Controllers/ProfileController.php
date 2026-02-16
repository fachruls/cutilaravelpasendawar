<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Wajib ada untuk hapus file lama
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     * BAGIAN INI SUDAH DIAMANKAN DARI MASS ASSIGNMENT
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // === SECURITY FIX: MASS ASSIGNMENT PROTECTION ===
        // Kita HANYA mengambil field yang aman untuk diupdate user biasa.
        // Field 'role', 'jabatan', 'cuti_n' akan DIBUANG otomatis di sini.
        // Hacker tidak bisa lagi mengubah jabatan sendiri.
        
        $safeData = $request->only(['name', 'email', 'no_hp', 'alamat']);
        
        // Masukkan data aman ke model user
        $request->user()->fill($safeData);

        // Jika email berubah, reset status verifikasi
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Upload Spesimen Tanda Tangan Digital
     */
    public function uploadTtd(Request $request): RedirectResponse
    {
        // 1. Validasi File (Wajib PNG agar transparan)
        $request->validate([
            'ttd_image' => 'required|image|mimes:png|max:2048', 
        ], [
            'ttd_image.required' => 'File tanda tangan wajib diupload.',
            'ttd_image.mimes' => 'Format file harus PNG (agar background transparan).',
            'ttd_image.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $user = $request->user();

        // 2. Hapus file lama jika ada (Hemat Storage)
        if ($user->ttd_path && Storage::disk('public')->exists($user->ttd_path)) {
            Storage::disk('public')->delete($user->ttd_path);
        }

        // 3. Simpan file baru
        $filename = 'ttd_user_' . $user->id . '_' . time() . '.png';
        $path = $request->file('ttd_image')->storeAs(
            'tanda_tangan_profil', // Folder tujuan
            $filename, 
            'public'
        );

        // 4. Update Database
        $user->forceFill([
            'ttd_path' => $path
        ])->save();

        return Redirect::route('profile.edit')->with('status', 'tanda-tangan-updated');
    }
}