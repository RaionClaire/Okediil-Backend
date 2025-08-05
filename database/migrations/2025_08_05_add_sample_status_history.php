<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add sample status history data for testing
        // This creates a realistic timeline for existing transactions
        
        // Get first few transactions to add sample history
        $transactions = DB::table('transaksi')->limit(3)->get();
        
        foreach ($transactions as $transaction) {
            $baseDate = now()->subDays(7); // Start from 7 days ago
            
            // Sample timeline for each transaction
            $statusTimeline = [
                ['status' => 'pending', 'hours_offset' => 0, 'note' => 'Transaksi dibuat, menunggu teknisi'],
                ['status' => 'Diagnosa', 'hours_offset' => 2, 'note' => 'Teknisi mulai melakukan diagnosis'],
                ['status' => 'Proses', 'hours_offset' => 24, 'note' => 'Kerusakan ditemukan, mulai perbaikan'],
                ['status' => 'Pengujian', 'hours_offset' => 48, 'note' => 'Perbaikan selesai, mulai pengujian'],
                ['status' => 'Siap Diambil', 'hours_offset' => 72, 'note' => 'Pengujian berhasil, siap diambil customer'],
            ];
            
            $previousStatus = null;
            
            foreach ($statusTimeline as $index => $timeline) {
                $changeTime = $baseDate->copy()->addHours($timeline['hours_offset']);
                
                DB::table('transaksi_status_history')->insert([
                    'id_transaksi' => $transaction->id_transaksi,
                    'status_lama' => $previousStatus,
                    'status_baru' => $timeline['status'],
                    'changed_by' => $transaction->id_karyawan,
                    'catatan_perubahan' => $timeline['note'],
                    'changed_at' => $changeTime,
                    'created_at' => $changeTime,
                    'updated_at' => $changeTime
                ]);
                
                $previousStatus = $timeline['status'];
            }
            
            // Update the transaction to have the latest status
            DB::table('transaksi')
                ->where('id_transaksi', $transaction->id_transaksi)
                ->update(['status_transaksi' => 'Siap Diambil']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove sample data
        DB::table('transaksi_status_history')->truncate();
    }
};
