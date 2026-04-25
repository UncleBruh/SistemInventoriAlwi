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
        'jumlah_terjual',
        'harga_per_unit',
        'total_harga',
        'tanggal_penjualan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'datetime',
        'harga_per_unit' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function makanan()
    {
        return $this->belongsTo(Makanan::class, 'id_makanan', 'id_makanan');
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }
}
