<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Wajib ada untuk fitur hapus/simpan file
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
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

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
     * FITUR BARU: Upload Spesimen Tanda Tangan Digital
     * Digunakan untuk penandatanganan otomatis surat cuti.
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

        // 2. Hapus file lama jika ada (untuk menghemat storage)
        if ($user->ttd_path && Storage::disk('public')->exists($user->ttd_path)) {
            Storage::disk('public')->delete($user->ttd_path);
        }

        // 3. Simpan file baru
        // Nama file dibuat unik: ttd_user_{ID}_{TIMESTAMP}.png
        $filename = 'ttd_user_' . $user->id . '_' . time() . '.png';
        $path = $request->file('ttd_image')->storeAs(
            'tanda_tangan_profil', // Folder tujuan di storage/app/public/
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