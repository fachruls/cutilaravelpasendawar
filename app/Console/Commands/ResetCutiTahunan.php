<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetCutiTahunan extends Command
{
    protected $signature = 'cuti:reset-tahunan {--force : Jalankan tanpa konfirmasi}';
    protected $description = 'Reset saldo cuti tahunan seluruh pegawai di awal tahun baru (N→N-1, N-1→N-2, N=hak_cuti)';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('Yakin ingin mereset saldo cuti seluruh pegawai? Aksi ini tidak bisa dibatalkan.')) {
            $this->info('Dibatalkan.');
            return 0;
        }

        $tahun = date('Y');
        $pegawai = User::whereIn('role', ['pegawai', 'pimpinan', 'kasubag'])->get();
        $totalReset = 0;

        foreach ($pegawai as $user) {
            $sisa_n = (int) $user->cuti_n;
            $sisa_n1 = (int) $user->cuti_n1;
            $hak = (int) ($user->hak_cuti_tahunan ?? 12);

            // Geser: N-1 sisa → N-2 (max 6), N sisa → N-1 (max 6), N = reset penuh
            $new_n2 = min($sisa_n1, 6);
            $new_n1 = min($sisa_n, 6);
            $new_n  = $hak;

            $user->update([
                'cuti_n2' => $new_n2,
                'cuti_n1' => $new_n1,
                'cuti_n'  => $new_n,
            ]);

            $totalReset++;
            $this->line("  ✓ {$user->name}: N-2={$new_n2}, N-1={$new_n1}, N={$new_n}");
        }

        // Catat di Audit Log
        AuditLog::create([
            'user_id'    => User::where('role', 'admin')->value('id') ?? 1,
            'action'     => 'RESET_CUTI_TAHUNAN',
            'details'    => "Sistem mereset saldo cuti {$totalReset} pegawai untuk tahun {$tahun}.",
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Artisan CLI',
        ]);

        $this->info("\n✅ Selesai! {$totalReset} pegawai berhasil direset untuk tahun {$tahun}.");
        Log::info("CUTI RESET: {$totalReset} pegawai direset untuk tahun {$tahun}.");

        return 0;
    }
}
