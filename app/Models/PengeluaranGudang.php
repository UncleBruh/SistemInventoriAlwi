<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengeluaranGudang extends Model
{
    protected $table = 'pengeluaran_gudang';
    protected $primaryKey = 'id_pengeluaran_gudang';

    protected $fillable = [
        'id_makanan',
        'id_pengguna',
        'jumlah_keluar',
        'stok_gudang_sebelum',
        'stok_gudang_sesudah',
        'alasan',
        'keterangan',
        'tgl_pengeluaran',
        'barcode'
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
