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
        Schema::table('penjualans', function (Blueprint $table) {
            // Hapus kolom kode_transaksi
            if (Schema::hasColumn('penjualans', 'kode_transaksi')) {
                $table->dropColumn('kode_transaksi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            // Kembalikan kolom kode_transaksi jika di-rollback
            $table->string('kode_transaksi')->nullable()->unique()->after('id_penjualan');
        });
    }
};
