<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crm extends Model
{
    protected $table = 'crm';
    protected $primaryKey = 'id_crm';
    protected $fillable = [
        'nama',
        'tanggal',
        'alamat',
        'no_hp',
        'jenis_kelamin',
        'pekerjaan',
        'sumber_chat',
        'jenis_produk',
        'kondisi',
        'merk',
        'tipe_produk',
        'status',
    ];
}
