<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controller Umum
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChangePasswordController;

// Controller Pegawai
use App\Http\Controllers\CutiController;

// Controller Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\JenisCutiController;
use App\Http\Controllers\Admin\HariLiburController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\CetakController;
use App\Http\Controllers\Admin\LaporanController;

// Controller Pimpinan & Kasubag
use App\Http\Controllers\PersetujuanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Depan (Landing Page)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// DASHBOARD UTAMA
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ====================================================
// GROUP ADMIN
// ====================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('pegawai', PegawaiController::class);
    Route::resource('jenis-cuti', JenisCutiController::class)->except(['show', 'create', 'edit']);
    Route::resource('hari-libur', HariLiburController::class)->except(['show', 'create', 'edit']);
    Route::get('/audit-trail', [AuditController::class, 'index'])->name('audit');
    Route::get('/kalender', [AdminDashboardController::class, 'kalender'])->name('kalender');
    Route::get('/cuti/{id}/cetak-formulir', [CetakController::class, 'formulir'])->name('cuti.cetak_formulir');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/export', [LaporanController::class, 'export'])
        ->middleware('throttle:10,1') 
        ->name('laporan.export');
});


// ====================================================
// GROUP PERSETUJUAN (PIMPINAN, ATASAN & KASUBAG)
// ====================================================
Route::middleware(['auth'])->prefix('persetujuan')->name('pimpinan.')->group(function () {
    Route::get('/', [PersetujuanController::class, 'index'])->name('persetujuan.index');
    
    Route::match(['put', 'post'], '/{id}/setuju', [PersetujuanController::class, 'setuju'])
        ->middleware('throttle:10,1')
        ->name('persetujuan.setuju');
        
    Route::match(['put', 'post'], '/{id}/tolak', [PersetujuanController::class, 'tolak'])
        ->middleware('throttle:10,1')
        ->name('persetujuan.tolak');
});


// ====================================================
// GROUP PIMPINAN (KHUSUS DASHBOARD)
// ====================================================
Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pimpinanIndex'])->name('dashboard');
    Route::get('/kalender', [DashboardController::class, 'kalender'])->name('kalender');
});

// ====================================================
// [TAMBAHAN] GROUP KASUBAG KEPEGAWAIAN
// ====================================================
Route::middleware(['auth', 'role:kasubag'])->prefix('kasubag')->name('kasubag.')->group(function () {
    // Kasubag akses dashboard logic yg sama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/rekap-cuti', [App\Http\Controllers\PersetujuanController::class, 'rekap'])->name('rekap');
});


// ====================================================
// GROUP PEGAWAI
// ====================================================
Route::middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/ajukan-cuti', [CutiController::class, 'create'])->name('cuti.create');
    Route::post('/ajukan-cuti', [CutiController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('cuti.store');
    
    Route::get('/riwayat-cuti', [CutiController::class, 'index'])->name('cuti.index');
    Route::delete('/cuti/{id}', [CutiController::class, 'destroy'])->name('cuti.destroy');
    Route::post('/cuti/hitung', [CutiController::class, 'hitungHari'])->name('cuti.hitung');
    Route::get('/cuti/{id}/cetak', [CetakController::class, 'formulir'])->name('cuti.cetak');
});


// ====================================================
// FITUR UMUM (Profile, Password, Plh)
// ====================================================
Route::middleware('auth')->group(function () {
    Route::get('/ubah-password', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::put('/ubah-password', [ChangePasswordController::class, 'update'])->name('password.change.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/upload-ttd', [ProfileController::class, 'uploadTtd'])->name('profile.upload_ttd');
    Route::post('/set-plh', [DashboardController::class, 'updatePlh'])->name('plh.update');
    Route::get('/notifications/read-all', function() {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read_all');
});

require __DIR__.'/auth.php';