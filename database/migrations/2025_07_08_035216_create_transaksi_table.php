<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('transaksi', function (Blueprint $table) {
    $table->id('id_transaksi');
    $table->string('id_customer', 20);
    $table->string('id_karyawan', 20);
    $table->string('servis_layanan', 10);
    $table->string('merk', 20);
    $table->string('tipe', 20);
    $table->string('warna', 20);
    $table->date('tanggal_masuk');
    $table->date('tanggal_keluar')->nullable();
    $table->string('tambahan', 100)->nullable();
    $table->string('catatan', 1000)->nullable();
    $table->string('keluhan', 1000)->nullable();
    $table->string('kelengkapan', 100)->nullable();
    $table->string('pin', 15)->nullable();
    $table->string('kerusakan', 1000)->nullable();
    $table->unsignedBigInteger('id_pembelian')->nullable();
    $table->integer('kuantitas');
    $table->integer('garansi')->nullable();
    $table->decimal('total_biaya', 10, 2);
    $table->string('status_transaksi', 20);
    $table->string('teknisi', 20)->nullable();
    $table->timestamps();

    $table->foreign('id_customer')->references('id_customer')->on('customers');
    $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan');
    $table->foreign('teknisi')->references('id_karyawan')->on('karyawan');

});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
