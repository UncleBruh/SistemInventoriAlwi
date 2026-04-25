<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Minuman extends Model
{
    protected $table = 'minuman';
    protected $primaryKey = 'id_minuman';

    protected $fillable = [
        'barcode',
        'nama_minuman',
        'harga',
        'stok',
        'stok_gudang',
        'stok_etalase',
    ];
}
