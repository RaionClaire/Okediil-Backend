<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    protected $table = 'aset'; // Nama tabel
    protected $primaryKey = 'id_aset'; // Kunci primer
    public $incrementing = true; // Kunci primer auto-increment
    protected $keyType = 'int'; // Tipe data kunci primer

    protected $fillable = [
        'nama_aset',
        'barcode',
        'jenis_aset',
        'kondisi',
        'tanggal_pembelian',
        'harga',
        'lokasi',
        'garansi',
        'jumlah',
        'catatan'
    ];
}
