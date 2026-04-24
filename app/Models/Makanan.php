<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makanan extends Model
{
    protected $table = 'makanan';
    protected $primaryKey = 'id_makanan';

    // Kolom apa saja yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'id_kategori', // <-- Tambahkan ini agar id_kategori bisa disimpan
        'barcode',
        'nama_makanan',
        'jenis_makanan',
        'harga',
        'stok',
    ];

    // Relasi ke tabel Kategori (Baru Ditambahkan)
    public function kategori()
    {
        // Menghubungkan Makanan ke Kategori berdasarkan id_kategori
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    // Relasi: Satu Makanan bisa memiliki banyak Log Aktivitas
    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'id_makanan', 'id_makanan');
    }
}