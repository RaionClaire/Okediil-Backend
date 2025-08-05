<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiStatusHistory extends Model
{
    protected $table = 'transaksi_status_history';
    
    protected $fillable = [
        'id_transaksi',
        'status_lama',
        'status_baru',
        'changed_by',
        'catatan_perubahan',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    // Relationship to transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    // Relationship to karyawan who made the change
    public function changedBy()
    {
        return $this->belongsTo(Karyawan::class, 'changed_by', 'id_karyawan');
    }

    // Scope to get history for a specific transaction
    public function scopeForTransaction($query, $transactionId)
    {
        return $query->where('id_transaksi', $transactionId)->orderBy('changed_at', 'desc');
    }

    // Scope to get recent status changes
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('changed_at', '>=', now()->subDays($days));
    }
}
