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
    Schema::create('log_aktivitas', function (Blueprint $table) {
        $table->id('id_log');
        
        $table->unsignedBigInteger('id_makanan');
        $table->unsignedBigInteger('id_pengguna');
        
        $table->string('jenis_aktivitas', 30)->comment('Contoh: Barang Masuk, Barang Keluar');
        $table->integer('jumlah_perubahan');
        $table->integer('stok_sebelum');
        $table->integer('stok_sesudah');
        $table->dateTime('tgl_aktivitas');
        $table->timestamps();

        $table->foreign('id_makanan')
              ->references('id_makanan')->on('makanan')
              ->onDelete('cascade')
              ->onUpdate('cascade');
              
        $table->foreign('id_pengguna')
              ->references('id_pengguna')->on('pengguna')
              ->onDelete('cascade')
              ->onUpdate('cascade');
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
