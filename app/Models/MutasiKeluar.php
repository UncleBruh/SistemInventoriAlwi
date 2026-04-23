<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiKeluar extends Model
{
    protected $table = 'mutasi_keluar';
    protected $primaryKey = 'id_mutasi_keluar';

    protected $fillable = [
        'id_makanan',
        'id_pengguna',
        'jumlah_keluar',
        'stok_sebelum',
        'stok_sesudah',
        'alasan', // Pastikan alasan masuk ke fillable
        'tgl_mutasi',
    ];

    public function makanan()
    {
        return $this->belongsTo(Makanan::class, 'id_makanan', 'id_makanan');
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}