<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reset Saldo Cuti Tahunan: setiap 1 Januari jam 00:01
Schedule::command('cuti:reset-tahunan --force')
    ->yearlyOn(1, 1, '00:01')
    ->timezone('Asia/Makassar')
    ->appendOutputTo(storage_path('logs/cuti-reset.log'));
