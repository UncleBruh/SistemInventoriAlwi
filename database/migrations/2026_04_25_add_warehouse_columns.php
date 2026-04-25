<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom stok_gudang dan stok_etalase ke tabel makanan dan minuman
     * Migrating dari single stok column ke warehouse/display stock
     */
    public function up(): void
    {
        // Untuk tabel makanan
        Schema::table('makanan', function (Blueprint $table) {
            // Tambah kolom baru untuk stok gudang dan etalase
            $table->integer('stok_gudang')->default(0)->after('stok');
            $table->integer('stok_etalase')->default(0)->after('stok_gudang');
        });

        // Untuk tabel minuman
        Schema::table('minuman', function (Blueprint $table) {
            // Tambah kolom baru untuk stok gudang dan etalase
            $table->integer('stok_gudang')->default(0)->after('stok');
            $table->integer('stok_etalase')->default(0)->after('stok_gudang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('makanan', function (Blueprint $table) {
            $table->dropColumn(['stok_gudang', 'stok_etalase']);
        });

        Schema::table('minuman', function (Blueprint $table) {
            $table->dropColumn(['stok_gudang', 'stok_etalase']);
        });
    }
};
