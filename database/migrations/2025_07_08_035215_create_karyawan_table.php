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
Schema::create('karyawan', function (Blueprint $table) {
    $table->string('id_karyawan', 6)->primary();
    $table->string('nama', 50);
    $table->char('jenis_kelamin', 1);
    $table->string('tempat_tanggal_lahir', 50);
    $table->string('alamat', 150);
    $table->string('no_hp', 15);
    $table->date('tanggal_masuk');
    $table->string('bidang', 20)->nullable();
    $table->string('status_karyawan', 20)->nullable();
    $table->string('cabang', 20)->nullable();
    $table->string('ukuran_baju', 5)->nullable();
    $table->date('tanggal_resign')->nullable();
    $table->timestamps();
    $table->string('password');
    $table->string('role', 20);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
