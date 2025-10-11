<?php

namespace Database\Seeders;

use App\Models\Pengeluaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SeederPengeluaran extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run()
    {
        $faker = Faker::create('id_ID');

        collect(range(1, 20))->each(function ($i) use ($faker) {
            Pengeluaran::create([
                'nama_pengeluaran' => $faker->word(),
                'jenis_pengeluaran' => $faker->randomElement(['ATK', 'Konsumsi', 'Aset']),
                'harga' => $faker->numberBetween(10000, 10000000),
                'kuantitas' => $faker->numberBetween(1, 10),
                'tanggal' => $faker->date(),
                'lokasi' => $faker->numberBetween(1, 5),
                'catatan' => $faker->sentence(),
            ]);
        });
    }
}
