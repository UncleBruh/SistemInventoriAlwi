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
        'lokasi_tujuan',
        'stok_gudang_sebelum',
        'stok_gudang_sesudah',
        'stok_etalase_sebelum',
        'stok_etalase_sesudah',
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