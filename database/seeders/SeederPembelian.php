<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Pembelian;

class SeederPembelian extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run()
    {
        $faker = Faker::create('id_ID');

        collect(range(1, 20))->each(function ($i) use ($faker) {
            Pembelian::create([
                'nama_produk' => $faker->word(),
                'kategori_produk' => $faker->randomElement(['Periferal', 'LCD', 'Baterai', 'Charger', 'Aksesoris']),
                'jenis_produk' => $faker->randomElement(['HP', 'Laptop']),
                'merk' => $faker->randomElement(['Apple', 'Samsung', 'Xiaomi', 'Oppo', 'Vivo', 'Asus', 'Acer', 'Lenovo', 'HP', 'Dell']),
                'kualitas_produk' => $faker->randomElement(['Original', 'Distributor', 'KW']),
                'harga_beli' => $faker->numberBetween(100000, 5000000),
                'nama_mitra' => $faker->randomElement(['Tokopedia', 'Shopee', 'Lazada', 'Bukalapak', 'Blibli', 'CV. Caca Sejahtera']),
                'ongkir' => $faker->numberBetween(1000, 50000),
                'metode_pembayaran' => $faker->randomElement(['Cash', 'Transfer', 'Debit']),
                'jumlah_produk' => 1,
                'tanggal' => $faker->date(),
                'garansi_produk' => $faker->date(),
            ]);
        });
    }
}
