<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIdKaryawanLength extends Migration
{
    public function up(): void
    {
        // Drop foreign keys first
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
            $table->dropForeign(['teknisi']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
        });

        // Modify the main column
        Schema::table('karyawan', function (Blueprint $table) {
            $table->string('id_karyawan', 20)->change(); // increase size
        });

        // Modify related foreign key columns
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('id_karyawan', 20)->change();
            $table->string('teknisi', 20)->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('id_karyawan', 20)->change();
        });

        // Re-add foreign keys
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan');
            $table->foreign('teknisi')->references('id_karyawan')->on('karyawan');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Reverse changes if needed
    }
}
