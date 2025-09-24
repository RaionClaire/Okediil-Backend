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
Schema::create('customers', function (Blueprint $table) {
    $table->string('id_customer', 20)->primary();
    $table->string('nama', 50);
    $table->string('email', 50);
    $table->string('no_hp', 20);
    $table->string('alamat', 20);
    $table->char('jenis_kelamin', 20);
    $table->string('status_pekerjaan', 10);
    $table->string('sumber', 15);
    $table->string('media_sosial', 20)->nullable();
    $table->integer('berapa_kali_servis')->default(0);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
