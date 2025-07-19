<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Karyawan extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_karyawan', 'nama', 'jenis_kelamin', 'tempat_tanggal_lahir', 
        'alamat', 'no_hp', 'tanggal_masuk', 'bidang', 'status_karyawan', 
        'cabang', 'ukuran_baju', 'tanggal_resign', 'password', 'role'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_resign' => 'date',
    ];

    // Override untuk login dengan id_karyawan
    public function getAuthIdentifierName()
    {
        return 'id_karyawan';
    }

    public function getAuthIdentifier()
    {
        return $this->getAttribute('id_karyawan');
    }

    // Helper methods untuk role
    public function isSuperAdmin()
    {
        return strtolower($this->role) === 'superadmin';
    }

    public function isAdmin()
    {
        return strtolower($this->role) === 'admin';
    }

    public function isTeknisi()
    {
        return strtolower($this->role) === 'teknisi';
    }

    // Check if active employee
    public function isActive()
    {
        return strtolower($this->status_karyawan) === 'aktif';
    }
}