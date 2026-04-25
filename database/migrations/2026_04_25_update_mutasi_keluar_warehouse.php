<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update tabel mutasi_keluar untuk mendukung fitur gudang
     * Mutasi keluar hanya akan mempengaruhi stok etalase
     */
    public function up(): void
    {
        Schema::table('mutasi_keluar', function (Blueprint $table) {
            // Update kolom alasan untuk lebih deskriptif
            $table->enum('tipe_keluar', ['penjualan', 'rusak', 'hilang', 'lainnya'])->default('penjualan')->after('tgl_mutasi');
            // Tambah kolom stok etalase untuk tracking (stok gudang tidak akan terpengaruh)
            $table->integer('stok_etalase_sebelum')->default(0)->after('tipe_keluar');
            $table->integer('stok_etalase_sesudah')->default(0)->after('stok_etalase_sebelum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_keluar', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_keluar',
                'stok_etalase_sebelum',
                'stok_etalase_sesudah'
            ]);
        });
    }
};
