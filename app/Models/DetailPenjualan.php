<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualans';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_penjualan',
        'id_makanan',
        'harga_satuan',
        'jumlah',
        'subtotal'
    ];

    // Relasi ke barang (Jajanan)
    public function makanan()
    {
        return $this->belongsTo(Makanan::class, 'id_makanan', 'id_makanan');
    }

    // Relasi ke Induk (Struk)
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }
}