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
Schema::create('pengeluaran', function (Blueprint $table) {
    $table->id('id_pengeluaran');
    $table->string('nama_pengeluaran', 50);
    $table->string('jenis_pengeluaran', 20);
    $table->bigInteger('harga');
    $table->integer('kuantitas');
    $table->date('tanggal');
    $table->string('lokasi', 100)->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
