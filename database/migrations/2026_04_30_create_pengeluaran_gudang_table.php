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
        Schema::create('pengeluaran_gudang', function (Blueprint $table) {
            $table->id('id_pengeluaran_gudang');
            $table->unsignedBigInteger('id_makanan');
            $table->unsignedBigInteger('id_pengguna');
            $table->integer('jumlah_keluar');
            $table->integer('stok_gudang_sebelum');
            $table->integer('stok_gudang_sesudah');
            $table->enum('alasan', ['expired', 'tikus', 'rusak', 'lainnya']);
            $table->text('keterangan')->nullable();
            $table->date('tgl_pengeluaran');
            $table->string('barcode')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_makanan')->references('id_makanan')->on('makanan')->onDelete('cascade');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_gudang');
    }
};
