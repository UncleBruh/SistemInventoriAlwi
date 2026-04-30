<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This must run AFTER pengeluaran_gudang and mutasi_keluar tables are created
     */
    public function up(): void
    {
        // Update 1: tipe_keluar enum di mutasi_keluar
        // Tambah nilai 'expired' dan 'keperluan_prive' ke enum
        DB::statement("ALTER TABLE mutasi_keluar CHANGE COLUMN tipe_keluar tipe_keluar ENUM('penjualan', 'rusak', 'hilang', 'expired', 'keperluan_prive', 'lainnya') NOT NULL DEFAULT 'penjualan'");

        // Update 2: alasan enum di pengeluaran_gudang
        // Pertama, ubah data lama 'tikus' menjadi 'keperluan_prive'
        DB::statement("UPDATE pengeluaran_gudang SET alasan = 'keperluan_prive' WHERE alasan = 'tikus'");

        // Kemudian update enum values
        // dari: ['expired', 'tikus', 'rusak', 'lainnya']
        // ke: ['expired', 'keperluan_prive', 'rusak', 'lainnya']
        DB::statement("ALTER TABLE pengeluaran_gudang CHANGE COLUMN alasan alasan ENUM('expired', 'keperluan_prive', 'rusak', 'lainnya') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert tipe_keluar ke enum sebelumnya
        DB::statement("ALTER TABLE mutasi_keluar CHANGE COLUMN tipe_keluar tipe_keluar ENUM('penjualan', 'rusak', 'hilang', 'lainnya') NOT NULL DEFAULT 'penjualan'");

        // Revert data dari 'keperluan_prive' kembali ke 'tikus'
        DB::statement("UPDATE pengeluaran_gudang SET alasan = 'tikus' WHERE alasan = 'keperluan_prive'");

        // Revert alasan ke enum sebelumnya
        DB::statement("ALTER TABLE pengeluaran_gudang CHANGE COLUMN alasan alasan ENUM('expired', 'tikus', 'rusak', 'lainnya') NOT NULL");
    }
};
