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
        Schema::create('mitra', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedBigInteger('klasifikasi_mitra_id');
            $table->text('alamat')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('logo_mitra')->nullable();
            $table->timestamps();

            $table->foreign('klasifikasi_mitra_id')
                ->references('id')->on('klasifikasi_mitra')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra');
    }
};
