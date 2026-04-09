<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makanan extends Model
{
    protected $table = 'makanan';
    protected $primaryKey = 'id_makanan';

    // Kolom apa saja yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'barcode',
        'nama_makanan',
        'jenis_makanan',
        'harga',
        'stok',
    ];

    // Relasi: Satu Makanan bisa memiliki banyak Log Aktivitas
    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'id_makanan', 'id_makanan');
    }
}