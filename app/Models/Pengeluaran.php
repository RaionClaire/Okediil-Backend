<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran'; // Nama tabel
    protected $primaryKey = 'id_pengeluaran'; // Kunci primer
    public $incrementing = true; // Kunci primer auto-increment
    protected $keyType = 'int'; // Tipe data kunci primer

    protected $fillable = [
        'nama_pengeluaran',
        'jenis_pengeluaran',
        'harga',
        'kuantitas',
        'tanggal',
        'lokasi',
        'catatan',
    ];
}
