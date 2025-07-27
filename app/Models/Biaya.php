<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biaya extends Model
{
    protected $table = 'biaya';
    protected $primaryKey = 'id_biaya';

    protected $fillable = [
        'nama_biaya',
        'biaya',
        'jenis_biaya',
        'tanggal',
        'lokasi',
    ];

    public $timestamps = true;
}
