<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Beritahu Laravel nama tabel dan primary key yang benar
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // // Relasi: Satu Pengguna bisa melakukan banyak Log Aktivitas
    // public function logAktivitas()
    // {
    //     return $this->hasMany(LogAktivitas::class, 'id_pengguna', 'id_pengguna');
    // }
}
