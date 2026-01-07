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
        Schema::create('log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('mitra_id');
            $table->unsignedBigInteger('dokumen_id');
            $table->text('keterangan')->nullable();
            $table->string('contact_person')->nullable();
            $table->date('tanggal_log');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('mitra_id')
                ->references('id')->on('mitra')
                ->onDelete('cascade');

            $table->foreign('dokumen_id')
                ->references('id')->on('dokumen')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log');
    }
};
