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
            $table->string('nama');
            $table->date('tanggal');
            $table->string('alamat');
            $table->string('no_hp');
            $table->string('jenis_kelamin');
            $table->string('pekerjaan');
            $table->string('sumber_chat');
            $table->string('jenis_produk');
            $table->string('kondisi');
            $table->string('merk');
            $table->string('tipe_produk')->nullable();
            $table->string('status');
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
