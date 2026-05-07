<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop tabel retur jika sudah ada
        Schema::dropIfExists('retur');

        Schema::create('retur', function (Blueprint $table) {
            $table->id('id_retur');
            $table->unsignedBigInteger('id_penjualan'); // Transaksi yang diretur
            $table->unsignedBigInteger('id_makanan');   // Jajanan yang diretur
            $table->unsignedBigInteger('id_pengguna');  // Kasir yang memproses
            $table->integer('jumlah_retur');
            $table->integer('nominal_pengembalian'); // Jumlah uang yang dipotong dari laporan
            $table->text('alasan')->nullable();
            $table->date('tgl_retur');
            $table->timestamps();

            // Relasi ke tabel lain
            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualans')->onDelete('cascade');
            $table->foreign('id_makanan')->references('id_makanan')->on('makanan')->onDelete('cascade');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
};
