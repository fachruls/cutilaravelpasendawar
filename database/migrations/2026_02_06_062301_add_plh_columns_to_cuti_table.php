<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('cuti', function (Blueprint $table) {
        $table->boolean('is_plh_atasan')->default(0)->after('atasan_langsung');
        $table->boolean('is_plh_pejabat')->default(0)->after('pejabat_berwenang');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            //
        });
    }
};
