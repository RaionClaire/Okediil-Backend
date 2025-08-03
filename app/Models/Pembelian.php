<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian'; 
    protected $primaryKey = 'id_pembelian'; 
    public $incrementing = true; 
    protected $keyType = 'int';

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

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'id_pembelian', 'id_pembelian');
    }

    public function transaksis()
    {
        return $this->belongsToMany(Transaksi::class, 'cart', 'id_pembelian', 'id_transaksi');
    }
}
