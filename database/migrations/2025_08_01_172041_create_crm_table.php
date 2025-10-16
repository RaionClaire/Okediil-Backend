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
        Schema::create('crm', function (Blueprint $table) {
            $table->id('id_crm');
            $table->string('nama', 50);
            $table->date('tanggal');
            $table->string('alamat', 150);
            $table->string('no_hp', 20);
            $table->string('jenis_kelamin');
            $table->string('pekerjaan', 50);
            $table->string('sumber_chat', 20);
            $table->string('jenis_produk', 30);
            $table->string('kondisi', 200);
            $table->string('merk', 30);
            $table->string('tipe_produk', 30)->nullable();
            $table->string('status', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm');
    }
};
