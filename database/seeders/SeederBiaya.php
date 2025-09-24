<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Biaya;

class SeederBiaya extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run()
    {
        $faker = Faker::create('id_ID');

        collect(range(1, 20))->each(function ($i) use ($faker) {
            Biaya::create([
                'nama_biaya' => $faker->word(),
                'biaya' => $faker->numberBetween(10000, 10000000),
                'jenis_biaya' => $faker->randomElement(['Tetap', 'Tidak Tetap']),
                'tanggal' => $faker->date(),
                'lokasi' => $faker->numberBetween(1, 5),
            ]);
        });
    }
}
