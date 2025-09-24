<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'id_cart';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_transaksi',
        'id_pembelian',
    ];

    public function transaksi() {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function pembelian() {
        return $this->belongsTo(Pembelian::class, 'id_pembelian', 'id_pembelian');
    }
}
