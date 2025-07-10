<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Karyawan extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_karyawan',
        'nama',
        'jenis_kelamin',
        'tempat_tanggal_lahir',
        'alamat',
        'no_hp',
        'tanggal_masuk',
        'bidang',
        'status_karyawan',
        'cabang',
        'ukuran_baju',
        'tanggal_resign',
        'role',
        'password',
    ];

    protected $hidden = [
        'password'
    ];
}
