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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->unsignedBigInteger('jenis_dokumen_id');
            $table->string('nomor_dokumen_mitra')->nullable();
            $table->string('nomor_dokumen_undip')->nullable();
            $table->string('judul_dokumen');
            $table->unsignedBigInteger('status_id');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->timestamps();

            $table->foreign('mitra_id')
                ->references('id')->on('mitra')
                ->onDelete('cascade');

            $table->foreign('jenis_dokumen_id')
                ->references('id')->on('jenis_dokumen')
                ->onDelete('restrict');

            $table->foreign('status_id')
                ->references('id')->on('status')
                ->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
