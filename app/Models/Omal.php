<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Omal extends Model
{
    protected $table = 'omal'; 
    protected $primaryKey = 'id_omal';
    public $incrementing = true;
    protected $keyType = 'int'; 
    
    protected $fillable = [
        'tanggal',
        'status_omal',
        'keterangan',
        'harga'
    ];
}
