<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
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
    ];

    protected $dates = [
        'tanggal_masuk',
        'tanggal_resign',
    ];

    // Relationship with User
    public function user()
    {
        return $this->hasOne(User::class, 'id_karyawan', 'id_karyawan');
    }
}