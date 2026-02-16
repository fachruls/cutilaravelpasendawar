<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cuti;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    // 1. TAMPILKAN DAFTAR PEGAWAI
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['pegawai', 'pimpinan']);

        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->cari . '%')
                  ->orWhere('nip', 'like', '%' . $request->cari . '%');
            });
        }

        $pegawai = $query->orderBy('role', 'desc')
                         ->orderBy('name', 'asc')
                         ->paginate(10);
        
        return view('admin.pegawai.index', compact('pegawai'));
    }

    // 2. FORM TAMBAH
    public function create()
    {
        // Ambil list atasan jika diperlukan di view
        $atasanList = User::where('role', '!=', 'admin')->get();
        return view('admin.pegawai.create', compact('atasanList'));
    }

    // 3. SIMPAN PEGAWAI BARU
    public function store(Request $request)
    {
       dd('SAYA BACA FILE BARU'); // <--- TAMBAHKAN INI DULU
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|unique:users,nip',
            'username' => 'nullable|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6', 
            'role' => 'required',
            'hak_cuti_tahunan' => 'required|integer|min:0',
            'atasan_langsung' => 'nullable|string',
        ]);

        // Simpan User
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'nip' => $request->nip,
            'username' => $request->username,
            'password' => $request->password, // Akan di-hash otomatis oleh Model User
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'unit_kerja' => 'Pengadilan Agama Sendawar',
            'tmt_jabatan' => $request->tmt_jabatan,
            'golongan' => $request->golongan,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'atasan_langsung' => $request->atasan_langsung,
            'hak_cuti_tahunan' => $request->hak_cuti_tahunan,
            'atasan_id' => $request->atasan_id,
        ]);

        // [BARIS 89 - PERBAIKAN DI SINI]
        // Pastikan tulisannya 'details', BUKAN 'description'
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'TAMBAH_PEGAWAI',
            'details' => "Menambahkan pegawai baru: $user->name", // <--- INI KUNCINYA
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit($id)
    {
        $pegawai = User::findOrFail($id);
        $atasanList = User::where('id', '!=', $id)->where('role', '!=', 'admin')->get();
        return view('admin.pegawai.edit', compact('pegawai', 'atasanList'));
    }

    // 5. UPDATE PEGAWAI
    public function update(Request $request, $id)
    {
        $pegawai = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($pegawai->id)],
            'role' => 'required',
            'hak_cuti_tahunan' => 'required|integer|min:0',
        ]);

        $data = [
            'name' => $request->nama,
            'email' => $request->email,
            'nip' => $request->nip,
            'username' => $request->username,
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'tmt_jabatan' => $request->tmt_jabatan,
            'golongan' => $request->golongan,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'atasan_langsung' => $request->atasan_langsung,
            'hak_cuti_tahunan' => $request->hak_cuti_tahunan,
            'atasan_id' => $request->atasan_id,
        ];

        if ($request->filled('password_baru')) {
            $request->validate([
                'password_baru' => 'min:6'
            ]);
            $data['password'] = $request->password_baru;
        }

        $pegawai->update($data);

        // [PERBAIKAN JUGA DI SINI]
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'EDIT_PEGAWAI',
            'details' => "Mengupdate data pegawai: $pegawai->name", // <--- GANTI JADI details
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui!');
    }

    // 6. HAPUS PEGAWAI
    public function destroy($id)
    {
        $pegawai = User::findOrFail($id);

        if (Cuti::where('user_id', $id)->exists()) {
            return back()->withErrors(['msg' => 'Gagal hapus: Pegawai memiliki riwayat cuti.']);
        }

        $nama = $pegawai->name;
        $pegawai->delete();

        // [PERBAIKAN JUGA DI SINI]
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'HAPUS_PEGAWAI',
            'details' => "Menghapus pegawai: $nama", // <--- GANTI JADI details
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }
}