<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, make id_pengguna nullable in mutasi_masuk
        Schema::table('mutasi_masuk', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pengguna')->nullable()->change();
        });

        // Then drop and recreate foreign key for mutasi_masuk
        Schema::table('mutasi_masuk', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });

        // Make id_pengguna nullable in mutasi_keluar
        Schema::table('mutasi_keluar', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pengguna')->nullable()->change();
        });

        // Drop and recreate foreign key for mutasi_keluar
        Schema::table('mutasi_keluar', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });

        // For alokasi_gudang_etalase, also make it set null
        Schema::table('alokasi_gudang_etalase', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pengguna')->nullable()->change();
        });

        Schema::table('alokasi_gudang_etalase', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('mutasi_masuk', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
            $table->unsignedBigInteger('id_pengguna')->change();
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('mutasi_keluar', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
            $table->unsignedBigInteger('id_pengguna')->change();
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('alokasi_gudang_etalase', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
            $table->unsignedBigInteger('id_pengguna')->change();
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }
};
