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
Schema::create('aset', function (Blueprint $table) {
    $table->id('id_aset');
    $table->string('nama_aset', 50);
    $table->binary('barcode')->nullable(); // blob → binary
    $table->string('jenis_aset', 20);
    $table->string('kondisi', 20);
    $table->date('tanggal_pembelian');
    $table->bigInteger('harga');
    $table->string('lokasi', 100)->nullable();
    $table->date('garansi')->nullable();
    $table->integer('jumlah');
    $table->string('catatan', 1000)->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
