<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\CRM;

class SeederCRM extends Seeder
{
  public function run()
    {
        $faker = Faker::create('id_ID');

        collect(range(1, 20))->each(function ($i) use ($faker) {
            CRM::create([
                'tanggal' => $faker->date(),
                'nama' => $faker->name,
                'no_hp' => preg_replace('/[^\d]/', '', $faker->phoneNumber),
                'alamat' => $faker->randomElement(['Kedamaian', 'Rajabasa', 'Tanjung Karang', 'Teluk Betung', 'Way Halim', 'Sukarame', 'Kemiling', 'Enggal', 'Gedong Air', 'Lainnya']),
                'jenis_kelamin' => $faker->randomElement(['Laki-Laki', 'Perempuan']),
                'pekerjaan' => $faker->randomElement(['Pelajar', 'Mahasiswa', 'Umum', 'Pekerja']),
                'sumber_chat' => $faker->randomElement(['Instagram', 'Whatsapp']),
                'jenis_produk' => $faker->randomElement(['Handphone', 'Laptop']),
                'kondisi' => $faker->randomElement(['Mati total', 'Nyala tapi tidak berfungsi', 'Layar pecah', 'Baterai cepat habis', 'Masalah pada aplikasi', 'Masalah pada jaringan']),
                'merk' => $faker->randomElement(['Apple', 'Samsung', 'Xiaomi', 'Oppo', 'Vivo', 'Realme', 'Asus', 'Lenovo', 'Huawei', 'Lainnya']),
                'tipe_produk' => $faker->word(),
                'status' => $faker->randomElement(['Hot', 'Warm', 'Cold']),
            ]);
        });
    }
}
