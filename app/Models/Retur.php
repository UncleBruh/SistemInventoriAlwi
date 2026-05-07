<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;

    // Sesuaikan dengan nama tabel di migration Anda
    protected $table = 'retur';
    protected $primaryKey = 'id_retur';

    // Kolom yang boleh diisi
    protected $fillable = [
        'id_penjualan',
        'id_makanan',
        'id_pengguna',
        'jumlah_retur',
        'nominal_pengembalian',
        'alasan',
        'tgl_retur',
    ];

    // Relasi ke tabel Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    // Relasi ke tabel Makanan
    public function makanan()
    {
        return $this->belongsTo(Makanan::class, 'id_makanan', 'id_makanan');
    }

    // Relasi ke tabel Pengguna (atau User)
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }
}
