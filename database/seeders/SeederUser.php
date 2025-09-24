<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Schema;

class SeederUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update a default superadmin account
        $attrs = [
                'nama' => 'Superadmin',
                'jenis_kelamin' => 'Perempuan',
                'tempat_tanggal_lahir' => 'Jakarta, 1990-01-01',
                'alamat' => 'Jl. Contoh No. 1, Jakarta',
                'no_hp' => '081234567890',
                'tanggal_masuk' => '2019-08-24',
                'bidang' => 'Admin',
                'status_karyawan' => 'Aktif',
                'cabang' => '1',
                'ukuran_baju' => 'L',
                'tanggal_resign' => null,
                'role' => 'superadmin',
        ];

        $attrs2 = [
                'nama' => 'Killua Zoldyck',
                'jenis_kelamin' => 'Laki-Laki',
                'tempat_tanggal_lahir' => 'Jakarta, 1990-01-01',
                'alamat' => 'Jl. Contoh No. 1, Jakarta',
                'no_hp' => '081234567890',
                'tanggal_masuk' => '2019-08-24',
                'bidang' => 'Admin',
                'status_karyawan' => 'Aktif',
                'cabang' => '1',
                'ukuran_baju' => 'L',
                'tanggal_resign' => null,
                'role' => 'admin',
        ];

        $attrs3 = [
                'nama' => 'Lloyd Frontiera',
                'jenis_kelamin' => 'Laki-Laki',
                'tempat_tanggal_lahir' => 'Jakarta, 1990-01-01',
                'alamat' => 'Jl. Contoh No. 1, Jakarta',
                'no_hp' => '081234567890',
                'tanggal_masuk' => '2019-08-24',
                'bidang' => 'Teknisi',
                'status_karyawan' => 'Aktif',
                'cabang' => '1',
                'ukuran_baju' => 'L',
                'tanggal_resign' => null,
                'role' => 'teknisi',
        ];

        $attrs4 = [
                'nama' => 'Franky',
                'jenis_kelamin' => 'Laki-Laki',
                'tempat_tanggal_lahir' => 'Jakarta, 1990-01-01',
                'alamat' => 'Jl. Contoh No. 1, Jakarta',
                'no_hp' => '081234567890',
                'tanggal_masuk' => '2019-08-24',
                'bidang' => 'Teknisi',
                'status_karyawan' => 'Aktif',
                'cabang' => '1',
                'ukuran_baju' => 'XXL',
                'tanggal_resign' => null,
                'role' => 'teknisi',
        ];


        Karyawan::updateOrCreate(
            ['id_karyawan' => 'ccaleb'],
            $attrs
        );

        Karyawan::updateOrCreate(
            ['id_karyawan' => 'killuazoldyck'],
            $attrs2
        );

        Karyawan::updateOrCreate(
            ['id_karyawan' => 'lloyd'],
            $attrs3
        );

        Karyawan::updateOrCreate(
            ['id_karyawan' => 'franky'],
            $attrs4
        );
    }
}
