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
        Schema::table('mitra', function (Blueprint $table) {
            if (! Schema::hasColumn('mitra', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('unit', function (Blueprint $table) {
            if (! Schema::hasColumn('unit', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('status', function (Blueprint $table) {
            if (! Schema::hasColumn('status', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->foreign('mitra_id')
                ->references('id')->on('mitra')
                ->restrictOnDelete();
        });

        Schema::table('log', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->foreign('mitra_id')
                ->references('id')->on('mitra')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->foreign('mitra_id')
                ->references('id')->on('mitra')
                ->cascadeOnDelete();
        });

        Schema::table('log', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->foreign('mitra_id')
                ->references('id')->on('mitra')
                ->cascadeOnDelete();
        });

        Schema::table('status', function (Blueprint $table) {
            if (Schema::hasColumn('status', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('unit', function (Blueprint $table) {
            if (Schema::hasColumn('unit', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('mitra', function (Blueprint $table) {
            if (Schema::hasColumn('mitra', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
