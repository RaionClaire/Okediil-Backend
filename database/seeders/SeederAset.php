<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Aset;

class SeederAset extends Seeder
{
  public function run()
    {
        $faker = Faker::create('id_ID');

        collect(range(1, 20))->each(function ($i) use ($faker) {
            Aset::create([
                'nama_aset' => $faker->name,
                'jenis_aset' => $faker->randomElement(['Operasional', 'Pendukung']),
                'kondisi' => $faker->randomElement(['Baik', 'Rusak', 'Perawatan']),
                'tanggal_pembelian' => $faker->date(),
                'harga' => $faker->numberBetween(10000, 10000000),
                'garansi' => $faker->date(),
                'jumlah' => 1,
                'catatan' => $faker->sentence(),
            ]);
        });
    }
}
