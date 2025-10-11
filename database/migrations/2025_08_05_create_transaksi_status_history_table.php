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
        Schema::create('transaksi_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_transaksi');
            $table->string('status_lama', 20)->nullable(); 
            $table->string('status_baru', 20); 
            $table->string('changed_by', 6)->nullable(); 
            $table->text('catatan_perubahan')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi')->onDelete('cascade');
            $table->foreign('changed_by')->references('id_karyawan')->on('karyawan')->onDelete('set null');
            
            $table->index(['id_transaksi', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_status_history');
    }
};
