<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update tabel mutasi_masuk untuk mendukung fitur gudang
     */
    public function up(): void
    {
        Schema::table('mutasi_masuk', function (Blueprint $table) {
            // Tambah kolom untuk menentukan apakah masuk ke gudang atau etalase
            $table->enum('lokasi_tujuan', ['gudang', 'etalase'])->default('gudang')->after('tgl_mutasi');
            // Tambah kolom stok gudang untuk tracking
            $table->integer('stok_gudang_sebelum')->default(0)->after('lokasi_tujuan');
            $table->integer('stok_gudang_sesudah')->default(0)->after('stok_gudang_sebelum');
            $table->integer('stok_etalase_sebelum')->default(0)->after('stok_gudang_sesudah');
            $table->integer('stok_etalase_sesudah')->default(0)->after('stok_etalase_sebelum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_masuk', function (Blueprint $table) {
            $table->dropColumn([
                'lokasi_tujuan',
                'stok_gudang_sebelum',
                'stok_gudang_sesudah',
                'stok_etalase_sebelum',
                'stok_etalase_sesudah'
            ]);
        });
    }
};
