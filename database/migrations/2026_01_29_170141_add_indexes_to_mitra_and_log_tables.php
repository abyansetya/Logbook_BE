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
    // Optimasi Tabel Mitra
    Schema::table('mitra', function (Blueprint $table) {
        $table->index('nama');
        $table->index('klasifikasi_mitra_id');
    });

    // Optimasi Tabel Log
    Schema::table('log', function (Blueprint $table) {
        $table->index('dokumen_id');
        $table->index('tanggal_log');
    });
}

public function down(): void
{
    Schema::table('mitra', function (Blueprint $table) {
        $table->dropIndex(['nama']);
        $table->dropIndex(['klasifikasi_mitra_id']);
    });

    Schema::table('log', function (Blueprint $table) {
        $table->dropIndex(['dokumen_id']);
        $table->dropIndex(['tanggal_log']);
    });
}
};
