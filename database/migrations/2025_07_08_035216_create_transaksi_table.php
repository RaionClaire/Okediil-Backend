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
    $table->string('id_customer', 30);
    $table->string('id_karyawan', 30);
    $table->string('servis_layanan', 30);
    $table->string('merk', 30);
    $table->string('tipe', 30);
    $table->string('warna', 30);
    $table->date('tanggal_masuk');
    $table->date('tanggal_keluar')->nullable();
    $table->string('tambahan', 100)->nullable();
    $table->string('catatan', 300)->nullable();
    $table->string('keluhan', 200)->nullable();
    $table->string('kelengkapan', 100)->nullable();
    $table->string('pin', 30)->nullable();
    $table->string('kerusakan', 300)->nullable();
    $table->unsignedBigInteger('id_pembelian')->nullable();
    $table->integer('kuantitas');
    $table->integer('garansi')->nullable();
    $table->bigInteger('total_biaya')->nullable();
    $table->string('status_transaksi', 30);
    $table->string('teknisi', 30)->nullable();
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
