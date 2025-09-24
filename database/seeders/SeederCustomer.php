<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Customer;

class SeederCustomer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        collect(range(1, 20))->each(function ($i) use ($faker) {
            Customer::create([
                'id_customer' => preg_replace('/[^\d]/', '', $faker->phoneNumber),
                'nama' => $faker->name,
                'email' => $faker->email,
                'no_hp' => preg_replace('/[^\d]/', '', $faker->phoneNumber),
                'alamat' => $faker->randomElement(['Kedamaian', 'Rajabasa', 'Tanjung Karang', 'Teluk Betung', 'Way Halim', 'Sukarame', 'Labuhan Ratu', 'Kemiling', 'Lainnya']),
                'jenis_kelamin' => $faker->randomElement(['Laki-Laki', 'Perempuan']),
                'status_pekerjaan' => $faker->randomElement(['Pelajar', 'Mahasiswa', 'Umum']),
                'sumber' => $faker->randomElement(['Instagram', 'Facebook', 'Google', 'Teman', "Reklame"]),
                'media_sosial' => $faker->randomElement(['Instagram', 'Facebook', 'Twitter']),
                'berapa_kali_servis' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
