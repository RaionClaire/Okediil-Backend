<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Omal;

class SeederOmal extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run()
    {
        $faker = Faker::create('id_ID');

        collect(range(1, 13))->each(function ($i) use ($faker) {
            Omal::create([
                'tanggal' => $faker->date(),
                'status_omal' => $faker->randomElement(['Uang Masuk', 'Uang Keluar']),
                'keterangan' => $faker->sentence(),
                'harga' => $faker->numberBetween(10000, 1000000),
            ]);
        });
    }
}
