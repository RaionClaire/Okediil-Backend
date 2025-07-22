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
    Schema::table('transaksi', function (Blueprint $table) {
        $table->dropForeign(['id_pembelian']);
    });

    Schema::table('transaksi', function (Blueprint $table) {
        $table->dropColumn('id_pembelian');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
