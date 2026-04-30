<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualans';
    protected $primaryKey = 'id_penjualan';

    protected $fillable = [
        'id_makanan',
        'id_pengguna',
        'jumlah',
        'total_harga',
        'tgl_penjualan',
        'no_nota',
        'bayar',
        'kembalian'
    ];


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