<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            SeederUser::class,
            SeederUser2::class,
            SeederCustomer::class,
            SeederCRM::class,
            SeederAset::class,
            SeederOmal::class,
            SeederPengeluaran::class,
            SeederPembelian::class,
            SeederBiaya::class,
        ]);
    }

}
