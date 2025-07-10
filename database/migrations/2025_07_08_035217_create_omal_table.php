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
Schema::create('omal', function (Blueprint $table) {
    $table->id('id_omal');
    $table->date('tanggal');
    $table->string('status_omal', 20);
    $table->string('keterangan', 1000);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('omal');
    }
};
