<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_masuk', function (Blueprint $table) {
            $table->id('id_mutasi_masuk');
            
            $table->unsignedBigInteger('id_makanan');
            $table->unsignedBigInteger('id_pengguna');
            
            $table->integer('jumlah_masuk');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->dateTime('tgl_mutasi');
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

    public function down(): void
    {
        Schema::dropIfExists('mutasi_masuk');
    }
};