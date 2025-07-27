<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Customer;
use App\Models\Karyawan;
use App\Models\Transaksi;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        collect(range(1, 20))->each(function ($i) use ($faker) {
            Customer::create([
                'id_customer' => 'C' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'nama' => $faker->name,
                'email' => $faker->email,
                'no_hp' => $faker->phoneNumber,
                'alamat' => $faker->city,
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'status_pekerjaan' => $faker->randomElement(['Tetap', 'Kontrak', 'Freelance']),
                'sumber' => $faker->randomElement(['Instagram', 'Facebook', 'Website', 'Teman']),
                'media_sosial' => $faker->userName,
                'berapa_kali_servis' => $faker->numberBetween(0, 10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        collect(range(1, 15))->each(function ($i) use ($faker) {
            Karyawan::create([
                'id_karyawan' => 'K' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'nama' => $faker->name,
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'tempat_tanggal_lahir' => $faker->city . ', ' . $faker->date('Y-m-d'),
                'alamat' => $faker->address,
                'no_hp' => $faker->phoneNumber,
                'tanggal_masuk' => $faker->date(),
                'bidang' => $faker->randomElement(['Teknisi', 'Admin', 'CS', 'Logistik']),
                'status_karyawan' => $faker->randomElement(['Aktif', 'Resign', 'Cuti']),
                'cabang' => $faker->randomElement(['Bandung', 'Jakarta', 'Surabaya']),
                'ukuran_baju' => $faker->randomElement(['S', 'M', 'L', 'XL']),
                'tanggal_resign' => $faker->optional()->date(),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => $faker->randomElement(['admin', 'teknisi', 'superadmin']),
            ]);
        });
    }
}

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $customerIds = Customer::pluck('id_customer')->toArray();
        $karyawanIds = Karyawan::pluck('id_karyawan')->toArray();

        collect(range(1, 30))->each(function () use ($faker, $customerIds, $karyawanIds) {
            $tanggalMasuk = $faker->dateTimeBetween('-6 months', 'now');
            $tanggalKeluar = (clone $tanggalMasuk)->modify('+'.rand(1, 14).' days');

            Transaksi::create([
                'id_customer' => $faker->randomElement($customerIds),
                'id_karyawan' => $faker->randomElement($karyawanIds),
                'servis_layanan' => $faker->randomElement(['Ganti LCD', 'Servis Mesin', 'Upgrade RAM']),
                'merk' => $faker->randomElement(['Asus', 'Acer', 'HP', 'Lenovo', 'Dell']),
                'tipe' => strtoupper(Str::random(5)),
                'warna' => $faker->safeColorName,
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_keluar' => $faker->boolean(70) ? $tanggalKeluar : null,
                'tambahan' => $faker->optional()->sentence,
                'catatan' => $faker->optional()->paragraph,
                'keluhan' => $faker->sentence,
                'kelengkapan' => $faker->randomElement(['Charger', 'Dusbook', 'Tas']),
                'pin' => $faker->optional()->numerify('####'),
                'kerusakan' => $faker->sentence,
                'kuantitas' => $faker->numberBetween(1, 5),
                'garansi' => $faker->optional()->numberBetween(0, 12),
                'total_biaya' => $faker->randomFloat(2, 50000, 5000000),
                'status_transaksi' => $faker->randomElement(['Pending', 'Proses', 'Selesai', 'Batal']),
                'created_at' => now(),
                'updated_at' => now(),
                'teknisi' => $faker->name,
            ]);
        });
    }
}
