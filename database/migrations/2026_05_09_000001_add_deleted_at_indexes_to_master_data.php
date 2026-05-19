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
            $table->index('deleted_at', 'mitra_deleted_at_index');
        });

        Schema::table('unit', function (Blueprint $table) {
            $table->index('deleted_at', 'unit_deleted_at_index');
        });

        Schema::table('status', function (Blueprint $table) {
            $table->index('deleted_at', 'status_deleted_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status', function (Blueprint $table) {
            $table->dropIndex('status_deleted_at_index');
        });

        Schema::table('unit', function (Blueprint $table) {
            $table->dropIndex('unit_deleted_at_index');
        });

        Schema::table('mitra', function (Blueprint $table) {
            $table->dropIndex('mitra_deleted_at_index');
        });
    }
};
