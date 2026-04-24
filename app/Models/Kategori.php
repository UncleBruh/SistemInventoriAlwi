<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    // 1. Beritahu Laravel nama tabel yang benar secara paksa
    protected $table = 'kategori';
    
    // 2. Beritahu Laravel nama Primary Key-nya
    protected $primaryKey = 'id_kategori';
    
    // 3. Kolom yang boleh diisi
    protected $fillable = ['nama_kategori'];

    // 4. Relasi ke tabel makanan
    public function makanan()
    {
        return $this->hasMany(Makanan::class, 'id_kategori', 'id_kategori');
    }
}