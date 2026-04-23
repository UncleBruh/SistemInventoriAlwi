<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiMasuk extends Model
{
    protected $table = 'mutasi_masuk';
    protected $primaryKey = 'id_mutasi_masuk';

    protected $fillable = [
        'id_makanan',
        'id_pengguna',
        'jumlah_masuk',
        'stok_sebelum',
        'stok_sesudah',
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