<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Membuat tabel anak (detail keranjang)
        Schema::create('detail_penjualans', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_penjualan'); // Penghubung ke struk induk
            $table->unsignedBigInteger('id_makanan');   // Barang yang dibeli
            
            $table->integer('harga_satuan');
            $table->integer('jumlah');
            $table->integer('subtotal'); // harga_satuan * jumlah
            $table->timestamps();
        });

        // 2. Menambahkan kolom struk & pembayaran di tabel induk (penjualans)
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('no_nota')->nullable()->after('id_penjualan');
            $table->integer('bayar')->nullable()->after('total_harga');
            $table->integer('kembalian')->nullable()->after('bayar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualans');
        
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['no_nota', 'bayar', 'kembalian']);
        });
    }
};