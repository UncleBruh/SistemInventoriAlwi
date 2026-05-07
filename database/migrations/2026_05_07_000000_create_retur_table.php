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
        Schema::dropIfExists('retur');
        
        Schema::create('retur', function (Blueprint $table) {
            $table->id('id_retur');
            $table->unsignedBigInteger('id_penjualan');
            $table->unsignedBigInteger('id_makanan')->nullable();
            $table->integer('jumlah_retur');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_retur', 15, 2); // harga_satuan * jumlah_retur
            $table->string('alasan_retur');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('id_pengguna');
            $table->timestamp('tgl_retur')->useCurrent();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_penjualan')
                  ->references('id_penjualan')
                  ->on('penjualans')
                  ->onDelete('cascade');
            $table->foreign('id_makanan')
                  ->references('id_makanan')
                  ->on('makanan')
                  ->onDelete('set null');
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
};
