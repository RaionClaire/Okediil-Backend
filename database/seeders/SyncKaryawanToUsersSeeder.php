<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SyncKaryawanToUsersSeeder extends Seeder
{
    public function run(): void
    {
        $karyawans = Karyawan::all();

        foreach ($karyawans as $k) {
            User::updateOrCreate(
                ['id_karyawan' => $k->id_karyawan],
                [
                    'nama' => $k->nama,
                    'password' => Hash::make($k->password),
                    'role' => $k->role,
                ]
            );
        }
    }
}

