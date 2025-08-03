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
}
