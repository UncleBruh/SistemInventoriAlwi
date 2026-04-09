<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_makanan',
        'id_pengguna',
        'jenis_aktivitas',
        'jumlah_perubahan',
        'stok_sebelum',
        'stok_sesudah',
        'tgl_aktivitas',
    ];

    // Relasi balik (Belongs To) ke tabel Makanan
    public function makanan()
    {
        return $this->belongsTo(Makanan::class, 'id_makanan', 'id_makanan');
    }

    // Relasi balik (Belongs To) ke tabel Pengguna (menggunakan model User)
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}