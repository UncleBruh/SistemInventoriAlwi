<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;

    protected $table = 'retur';
    protected $primaryKey = 'id_retur';
    protected $fillable = [
        'id_penjualan',
        'id_makanan',
        'jumlah_retur',
        'harga_satuan',
        'total_retur',
        'alasan_retur',
        'keterangan',
        'id_pengguna',
        'tgl_retur',
    ];

    protected $casts = [
        'tgl_retur' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function makanan()
    {
        return $this->belongsTo(Makanan::class, 'id_makanan', 'id_makanan');
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }
}
