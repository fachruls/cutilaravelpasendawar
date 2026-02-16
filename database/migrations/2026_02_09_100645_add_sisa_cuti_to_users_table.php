<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // N = Tahun Ini (Default 12 hari)
        $table->integer('cuti_n')->default(12)->after('password'); 
        
        // N-1 = Sisa Tahun Lalu (Default 0, nanti diisi manual/sistem)
        $table->integer('cuti_n1')->default(0)->after('cuti_n');
        
        // N-2 = Sisa 2 Tahun Lalu (Opsional, Default 0)
        $table->integer('cuti_n2')->default(0)->after('cuti_n1');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['cuti_n', 'cuti_n1', 'cuti_n2']);
    });
}
};
