<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlokasiGudangEtalase extends Model
{
    protected $table = 'alokasi_gudang_etalase';
    protected $primaryKey = 'id_alokasi';

    protected $fillable = [
        'id_makanan',
        'id_minuman',
        'jumlah_dialokasi',
        'stok_gudang_sebelum',
        'stok_gudang_sesudah',
        'stok_etalase_sebelum',
        'stok_etalase_sesudah',
        'id_pengguna',
        'keterangan',
        'tgl_alokasi',
    ];

    protected $casts = [
        'tgl_alokasi' => 'datetime',
    ];

    public function makanan()
    {
        return $this->belongsTo(Makanan::class, 'id_makanan', 'id_makanan');
    }

    public function minuman()
    {
        return $this->belongsTo(Minuman::class, 'id_minuman', 'id_minuman');
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}
