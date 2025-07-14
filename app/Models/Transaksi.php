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
    'kelengkapan', 'pin', 'kerusakan', 'id_pembelian', 'kuantitas',
    'garansi', 'total_biaya', 'status_transaksi',
];

public function customer() {
    return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
}

public function karyawan() {
    return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
}

public function pembelian() {
    return $this->belongsTo(Pembelian::class, 'id_pembelian', 'id_pembelian');
}
}