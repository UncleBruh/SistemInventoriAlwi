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
        Schema::create('makanan', function (Blueprint $table) {
            $table->id('id_makanan');
            $table->string('nama_makanan');
            
            // INI PENGGANTI JENIS MAKANAN
            $table->unsignedBigInteger('id_kategori')->nullable(); 
            
            $table->string('barcode')->nullable()->unique();
            $table->integer('harga');
            $table->integer('stok')->default(0);
            $table->timestamps();

            // Hubungkan ke tabel kategori
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makanans');
    }
};
