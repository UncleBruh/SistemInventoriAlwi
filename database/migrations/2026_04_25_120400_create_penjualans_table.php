<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->unsignedBigInteger('id_makanan');
            $table->unsignedBigInteger('id_pengguna');
            $table->integer('jumlah_terjual');
            $table->decimal('harga_per_unit', 12, 2);
            $table->decimal('total_harga', 12, 2);
            $table->dateTime('tanggal_penjualan');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_makanan')->references('id_makanan')->on('makanan')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
