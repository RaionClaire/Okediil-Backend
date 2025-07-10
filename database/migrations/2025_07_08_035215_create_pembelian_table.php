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
Schema::create('pembelian', function (Blueprint $table) {
    $table->id('id_pembelian');
    $table->string('nama_produk', 50);
    $table->string('kategori_produk', 20);
    $table->string('merk', 20);
    $table->string('jenis_produk', 30);
    $table->date('tanggal');
    $table->integer('jumlah_produk');
    $table->string('kualitas_produk', 20);
    $table->date('garansi_produk');
    $table->string('nama_mitra', 50);
    $table->bigInteger('harga_beli');
    $table->bigInteger('ongkir');
    $table->string('metode_pembayaran', 20);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
