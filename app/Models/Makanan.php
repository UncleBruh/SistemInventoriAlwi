<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makanan extends Model
{
    protected $table = 'makanan';
    protected $primaryKey = 'id_makanan';

    // Kolom apa saja yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'id_kategori',
        'barcode',
        'nama_makanan',
        'jenis_makanan',
        'harga',
        'stok',
        'stok_gudang',
        'stok_etalase',
    ];

    // Relasi ke tabel Kategori (Baru Ditambahkan)
    public function kategori()
    {
        // Menghubungkan Makanan ke Kategori berdasarkan id_kategori
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}
