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
            $table->string('status_lama', 20)->nullable(); // Previous status
            $table->string('status_baru', 20); // New status
            $table->string('changed_by', 6)->nullable(); // ID karyawan who made the change
            $table->text('catatan_perubahan')->nullable(); // Optional notes about the change
            $table->timestamp('changed_at'); // When the status was changed
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi')->onDelete('cascade');
            $table->foreign('changed_by')->references('id_karyawan')->on('karyawan')->onDelete('set null');
            
            // Index for faster queries
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
