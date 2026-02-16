<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil List User untuk Dropdown Filter
        $users = User::orderBy('name', 'asc')->get();

        // 2. Query Dasar
        $query = AuditLog::with('user')->latest();

        // 3. Terapkan Filter jika ada input
        
        // Filter Tanggal
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('created_at', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        // Filter User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Action (Jenis Aktivitas)
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // 4. Ambil Data dengan Pagination (20 per halaman)
        $logs = $query->paginate(20)->withQueryString();

        return view('admin.audit.index', compact('logs', 'users'));
    }
}