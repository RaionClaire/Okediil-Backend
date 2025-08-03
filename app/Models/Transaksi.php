<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{

protected $table = 'transaksi'; 
protected $primaryKey = 'id_transaksi';
public $incrementing = true;

protected $fillable = [
    'id_customer', 'id_karyawan', 'servis_layanan', 'merk', 'tipe', 'warna',
    'tanggal_masuk', 'tanggal_keluar', 'tambahan', 'catatan', 'keluhan',
    'kelengkapan', 'pin', 'kerusakan', 'kuantitas',
    'garansi', 'total_biaya', 'status_transaksi', 'teknisi'
];

public function customer() {
    return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
}

public function karyawan() {
    return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
}

// Relationship through cart table to get multiple pembelian items
public function cartItems() {
    return $this->hasMany(Cart::class, 'id_transaksi', 'id_transaksi');
}

// Get pembelian items through cart
public function pembelianItems() {
    return $this->hasManyThrough(
        Pembelian::class, 
        Cart::class, 
        'id_transaksi', // Foreign key on cart table
        'id_pembelian', // Foreign key on pembelian table
        'id_transaksi', // Local key on transaksi table
        'id_pembelian'  // Local key on cart table
    );
}

}