<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            // 1. Index untuk Nomor (Sering dicari/Search)
            $table->index('nomor_dokumen_undip');
            $table->index('nomor_dokumen_mitra');

            // 2. Index untuk Filter (Dropdown)
            // Note: Foreign keys biasanya otomatis, tapi menambahkan index manual mempertegas query
            $table->index('status_id');
            $table->index('jenis_dokumen_id');

            // 3. Index untuk Sorting (ORDER BY)
            // Sangat berguna karena query index() Anda menggunakan ->orderBy('created_at', 'desc')
            $table->index('created_at');
            $table->index('tanggal_masuk');

            // 4. (Opsional) Fulltext index jika ingin pencarian judul sangat cepat
            $table->fullText('judul_dokumen');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            // Cara menghapus index jika migration di-rollback
            $table->dropIndex(['nomor_dokumen_undip']);
            $table->dropIndex(['nomor_dokumen_mitra']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['jenis_dokumen_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['tanggal_masuk']);
            $table->dropFullText(['judul_dokumen']);
        });
    }
};
