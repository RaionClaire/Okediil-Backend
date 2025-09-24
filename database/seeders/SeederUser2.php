<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Karyawan;

class SeederUser2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ids = ['ccaleb', 'killuazoldyck', 'lloyd', 'franky'];
        foreach ($ids as $idKaryawan) {
            $k = Karyawan::find($idKaryawan);

            if (!$k) {
                if (property_exists($this, 'command') && $this->command) {
                    $this->command->warn("Karyawan with ID '{$idKaryawan}' not found. Skipping user seed.");
                }
                continue;
            }

            User::updateOrCreate(
                ['id_karyawan' => $idKaryawan],
                [
                    'nama' => $k->nama,
                    'password' => Hash::make($idKaryawan),
                    'role' => $k->role,
                ]
            );
        }
}
}