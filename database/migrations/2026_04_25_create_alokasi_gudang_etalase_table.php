<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create table untuk mencatat alokasi barang dari gudang ke etalase
     */
    public function up(): void
    {
        Schema::create('alokasi_gudang_etalase', function (Blueprint $table) {
            $table->id('id_alokasi');
            $table->unsignedBigInteger('id_makanan')->nullable();
            $table->unsignedBigInteger('id_minuman')->nullable();
            $table->integer('jumlah_dialokasi');
            $table->integer('stok_gudang_sebelum');
            $table->integer('stok_gudang_sesudah');
            $table->integer('stok_etalase_sebelum');
            $table->integer('stok_etalase_sesudah');
            $table->unsignedBigInteger('id_pengguna');
            $table->string('keterangan')->nullable();
            $table->timestamp('tgl_alokasi')->useCurrent();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_makanan')->references('id_makanan')->on('makanan')->onDelete('set null');
            $table->foreign('id_minuman')->references('id_minuman')->on('minuman')->onDelete('set null');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alokasi_gudang_etalase');
    }
};
