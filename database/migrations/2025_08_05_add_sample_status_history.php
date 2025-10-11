<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Note: This migration file was originally used for seeding sample data.
     * The data seeding has been moved to SeederTransaksiStatusHistory.
     * This file is kept for migration history purposes only.
     */
    public function up(): void
    {
        // Migration already exists in 2025_08_05_create_transaksi_status_history_table.php
        // This file now serves as a placeholder for historical reference
        // Use: php artisan db:seed --class=SeederTransaksiStatusHistory to seed sample data
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed - the actual table is managed by the create migration
    }
};
