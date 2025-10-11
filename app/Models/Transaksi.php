<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

protected static function booted()
{
    static::updating(function ($transaksi) {
        if ($transaksi->isDirty('status_transaksi')) {
            $originalStatus = $transaksi->getOriginal('status_transaksi');
            $newStatus = $transaksi->status_transaksi;
            
            $changedBy = null;
            if (Auth::check()) {
                $user = Auth::user();
                $changedBy = $user->id_karyawan ?? null;
            }
            
            TransaksiStatusHistory::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'status_lama' => $originalStatus,
                'status_baru' => $newStatus,
                'changed_by' => $changedBy,
                'changed_at' => now(),
                'catatan_perubahan' => 'Status diubah dari "' . $originalStatus . '" ke "' . $newStatus . '"'
            ]);
        }
    });

    static::created(function ($transaksi) {
        $changedBy = null;
        if (Auth::check()) {
            $user = Auth::user();
            $changedBy = $user->id_karyawan ?? null;
        }

        TransaksiStatusHistory::create([
            'id_transaksi' => $transaksi->id_transaksi,
            'status_lama' => null,
            'status_baru' => $transaksi->status_transaksi,
            'changed_by' => $changedBy,
            'changed_at' => now(),
            'catatan_perubahan' => 'Transaksi dibuat dengan status "' . $transaksi->status_transaksi . '"'
        ]);
    });
}

public function customer() {
    return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
}

public function karyawan() {
    return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
}

// Relationship to status history
public function statusHistory() {
    return $this->hasMany(TransaksiStatusHistory::class, 'id_transaksi', 'id_transaksi')
                ->orderBy('changed_at', 'desc');
}

// Get latest status change
public function latestStatusChange() {
    return $this->hasOne(TransaksiStatusHistory::class, 'id_transaksi', 'id_transaksi')
                ->orderBy('changed_at', 'desc');
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
        'id_transaksi', 
        'id_pembelian', 
        'id_transaksi', 
        'id_pembelian'  
    );
}

}