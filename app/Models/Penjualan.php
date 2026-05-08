<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';
    protected $primaryKey = 'id_penjualan';

    // WAJIB ADA: no_nota, bayar, kembalian agar tidak ditolak saat klik BAYAR
    protected $fillable = [
        'id_makanan',
        'id_pengguna',
        'jumlah_terjual',
        'harga_per_unit',
        'total_harga',
        'tanggal_penjualan',
        'no_nota',
        'bayar',
        'kembalian'
    ];

    // Relasi ke tabel detail (Anak)
    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }

    public function makanan()
    {
        return $this->belongsTo(Makanan::class, 'id_makanan', 'id_makanan');
    }
}
