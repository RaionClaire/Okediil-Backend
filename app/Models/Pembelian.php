<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian'; // Nama tabel
    protected $primaryKey = 'id_pembelian'; // Kunci primer
    public $incrementing = true; // Kunci primer auto-increment
    protected $keyType = 'int'; // Tipe data kunci primer

    protected $fillable = [
        'nama_produk',
        'kategori_produk',
        'merk',
        'jenis_produk',
        'tanggal',
        'jumlah_produk',
        'kualitas_produk',
        'garansi_produk',
        'nama_mitra',
        'harga_beli',
        'ongkir',
        'metode_pembayaran',
        'status'
    ];
}
