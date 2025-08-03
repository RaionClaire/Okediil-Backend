<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers'; 
    protected $primaryKey = 'id_customer'; 
    public $incrementing = false; 
    protected $keyType = 'string'; 

    protected $fillable = [
        'id_customer',
        'nama',
        'email',
        'no_hp',
        'alamat',
        'jenis_kelamin',
        'status_pekerjaan',
        'sumber',
        'media_sosial',
        'berapa_kali_servis',
    ];

    protected $attributes = [
        'berapa_kali_servis' => 0,
    ];

    // Relationship to transaksi
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_customer', 'id_customer');
    }

    // Dynamic method to get actual service count
    public function getActualServiceCount()
    {
        return $this->transaksi()->count();
    }

    // Accessor to always return the real count
    public function getBerapaKaliServisAttribute()
    {
        return $this->transaksi()->count();
    }

    // Static method to sync all customer service counts (if you want to update the database field)
    public static function syncAllServiceCounts()
    {
        self::chunk(100, function ($customers) {
            foreach ($customers as $customer) {
                $actualCount = $customer->transaksi()->count();
                $customer->update(['berapa_kali_servis' => $actualCount]);
            }
        });
    }
}
