<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Transaksi;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\TransaksiStatusHistory;

class TransaksiController extends Controller
{

    public function testAuth(Request $request)
{ 
     if ($request->user('sanctum')) {
            return "auth";
      } else {
            return "guest";
      }
    }

public function store(Request $request) 
{
    $user = Auth::user('sanctum');
    if (!$user) {
        return response()->json([
            'message' => 'Unauthorized - Token tidak valid atau sudah expired',
            'debug' => [
                'auth_guard' => config('auth.defaults.guard'),
                'header_auth' => $request->header('Authorization'),
                'user_check' => Auth::check()
            ]
        ], 401);
    }

    $validated = $request->validate([
        'id_customer' => 'required|exists:customers,id_customer',
        'servis_layanan' => 'required|string',
        'merk' => 'required|string',
        'tipe' => 'required|string',
        'warna' => 'required|string',
        'tanggal_masuk' => 'required|date',
        'tanggal_keluar' => 'nullable|date',
        'tambahan' => 'nullable|string|max:1000',
        'catatan' => 'nullable|string|max:1000',
        'keluhan' => 'nullable|string|max:100',
        'kelengkapan' => 'nullable|string|max:100',
        'pin' => 'nullable|string|max:100',
        'kerusakan' => 'nullable|string|max:100',
        'kuantitas' => 'required|integer|min:1',
        'garansi' => 'nullable|integer',
        'total_biaya' => 'required|numeric',
        'status_transaksi' => 'required|string',
        'teknisi' => 'nullable|string|max:50',
        'pembelian_items' => 'nullable|array',
        'pembelian_items.*' => 'required_with:pembelian_items|exists:pembelian,id_pembelian',
    ]);

    $validated['id_karyawan'] = $user->id_karyawan;
    if (isset($validated['teknisi'])) {
        $teknisiExists = \App\Models\Karyawan::where('id_karyawan', $validated['teknisi'])
            ->whereIn('role', ['teknisi', 'superadmin'])
            ->exists();
        
        if (!$teknisiExists) {
            return response()->json([
                'message' => 'Teknisi tidak valid atau tidak memiliki role teknisi'
            ], 422);
        }
    }

    try {
        DB::beginTransaction();

        // Create transaksi (remove pembelian_items from the data)
        $transaksiData = $validated;
        unset($transaksiData['pembelian_items']);
        $transaksi = Transaksi::create($transaksiData);

        // Create cart entries for each pembelian item (only if items are provided)
        if (isset($validated['pembelian_items']) && !empty($validated['pembelian_items'])) {
            foreach ($validated['pembelian_items'] as $pembelianId) {
                Cart::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_pembelian' => $pembelianId,
                ]);

                // Update pembelian status to used (0)
                $pembelian = \App\Models\Pembelian::find($pembelianId);
                if ($pembelian) {
                    $pembelian->status = 0;
                    $pembelian->save();
                }
            }
        }

        // Update customer counter
        $customer = Customer::find($validated['id_customer']);
        if ($customer) {
            $customer->berapa_kali_servis += 1;
            $customer->save();
        }

        DB::commit();

        // Load the complete transaction with relationships
        $transaksi = Transaksi::with(['customer', 'karyawan', 'cartItems.pembelian'])->find($transaksi->id_transaksi);

        return response()->json([
            'message' => 'Transaksi berhasil ditambahkan',
            'id_transaksi' => $transaksi->id_transaksi,
            'data' => $transaksi,
            'user' => $user->nama,
        ], 201);

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'message' => 'Terjadi kesalahan',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function index() 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $query = Transaksi::with(['customer', 'karyawan', 'cartItems.pembelian']);
        
        // If user is teknisi, only show transactions assigned to them
        if ($user->role === 'teknisi') {
            $query->where('teknisi', $user->id_karyawan);
        }
        
        // Order by newest first
        $transaksi = $query->orderBy('created_at', 'desc')->get();
        return response()->json($transaksi);
    }

    public function show($id) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::with(['customer', 'karyawan', 'cartItems.pembelian'])->find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }
        return response()->json($transaksi);
    }

    public function updateStatus(Request $request, $id) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // If user is teknisi, only allow them to update their assigned transactions
        if ($user->role === 'teknisi' && $transaksi->teknisi !== $user->id_karyawan) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk mengupdate transaksi ini'], 403);
        }

        $validated = $request->validate([
            'status_transaksi' => 'required|string|in:pending,Diagnosa,Proses,Pengujian,Siap Diambil,Sudah Diambil,Gagal,Dibatalkan,completed,cancelled',
        ]);

        try {
            $oldStatus = $transaksi->status_transaksi;
            
            // Update status - the model event will automatically log this to history
            $transaksi->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Status transaksi berhasil diperbarui dari "' . $oldStatus . '" ke "' . $validated['status_transaksi'] . '"',
                'data' => $transaksi,
                'status_change' => [
                    'from' => $oldStatus,
                    'to' => $validated['status_transaksi'],
                    'changed_by' => $user->id_karyawan,
                    'changed_at' => now()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'id_customer' => 'sometimes|required|exists:customers,id_customer',
            'servis_layanan' => 'sometimes|required|string',
            'merk' => 'sometimes|required|string',
            'tipe' => 'sometimes|required|string',
            'warna' => 'sometimes|required|string',
            'tanggal_masuk' => 'sometimes|required|date',
            'tanggal_keluar' => 'nullable|date',
            'tambahan' => 'nullable|string|max:1000',
            'catatan' => 'nullable|string|max:1000',
            'keluhan' => 'nullable|string|max:100',
            'kelengkapan' => 'nullable|string|max:100',
            'pin' => 'nullable|string|max:100',
            'kerusakan' => 'nullable|string|max:100',
            'kuantitas' => 'sometimes|required|integer|min:1',
            'garansi' => 'nullable|integer',
            'total_biaya' => 'sometimes|required|numeric',
            'status_transaksi' => 'sometimes|required|string',
            'teknisi' => 'nullable|string|max:50',
            'pembelian_items' => 'sometimes|array',
            'pembelian_items.*' => 'required_with:pembelian_items|exists:pembelian,id_pembelian',
        ]);

        try {
            DB::beginTransaction();

            // Update transaksi (remove pembelian_items from the data)
            $transaksiData = $validated;
            unset($transaksiData['pembelian_items']);
            $transaksi->update($transaksiData);

            // If pembelian_items is provided, update cart entries
            if (isset($validated['pembelian_items'])) {
                // First, set previous pembelian items back to available (status = 1)
                $existingCartItems = Cart::where('id_transaksi', $id)->get();
                foreach ($existingCartItems as $cartItem) {
                    $pembelian = \App\Models\Pembelian::find($cartItem->id_pembelian);
                    if ($pembelian) {
                        $pembelian->status = 1;
                        $pembelian->save();
                    }
                }

                // Delete existing cart entries
                Cart::where('id_transaksi', $id)->delete();

                // Create new cart entries
                foreach ($validated['pembelian_items'] as $pembelianId) {
                    Cart::create([
                        'id_transaksi' => $id,
                        'id_pembelian' => $pembelianId,
                    ]);

                    // Update pembelian status to used (0)
                    $pembelian = \App\Models\Pembelian::find($pembelianId);
                    if ($pembelian) {
                        $pembelian->status = 0;
                        $pembelian->save();
                    }
                }
            }

            DB::commit();

            // Load the updated transaction with relationships
            $transaksi = Transaksi::with(['customer', 'karyawan', 'cartItems.pembelian'])->find($id);

            return response()->json([
                'message' => 'Transaksi berhasil diperbarui',
                'data' => $transaksi
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        try {
            DB::beginTransaction();

            // Set pembelian items back to available (status = 1) before deleting
            $cartItems = Cart::where('id_transaksi', $id)->get();
            foreach ($cartItems as $cartItem) {
                $pembelian = \App\Models\Pembelian::find($cartItem->id_pembelian);
                if ($pembelian) {
                    $pembelian->status = 1;
                    $pembelian->save();
                }
            }

            // Delete cart entries
            Cart::where('id_transaksi', $id)->delete();

            // Decrease customer service count before deleting transaction
            $customer = Customer::find($transaksi->id_customer);
            if ($customer && $customer->berapa_kali_servis > 0) {
                $customer->berapa_kali_servis -= 1;
                $customer->save();
            }

            // Delete transaksi
            $transaksi->delete();

            DB::commit();

            return response()->json(['message' => 'Transaksi berhasil dihapus']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function filter(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $query = Transaksi::with(['customer', 'karyawan', 'cartItems.pembelian']);

        if ($request->has('year')) {
            $query->whereYear('tanggal_masuk', $request->year);
        }

        if ($request->has('month')) {
            $query->whereMonth('tanggal_masuk', $request->month);
        }

        if ($request->has('status_transaksi')) {
            $query->where('status_transaksi', $request->status_transaksi);
        }

        return response()->json($query->get(), 200);
    }

    public function trackByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15'
        ]);

        $phone = $request->phone;
        
        // Additional security: Remove any special characters except + and numbers
        $normalizedPhone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Validate phone number format (Indonesian numbers only)
        if (!preg_match('/^(\+?628|08)[0-9]{8,11}$/', $normalizedPhone)) {
            return response()->json([
                'success' => false,
                'message' => 'Format nomor telepon tidak valid. Gunakan format Indonesia (08xxx atau 628xxx)',
                'data' => []
            ], 400);
        }

        // Create different possible formats for searching
        $phoneFormats = [];
        
        // If starts with 08, add 628 version
        if (substr($normalizedPhone, 0, 2) === '08') {
            $phoneFormats[] = $normalizedPhone; // Original 08xxx
            $phoneFormats[] = '628' . substr($normalizedPhone, 2); // Convert to 628xxx
            $phoneFormats[] = '+628' . substr($normalizedPhone, 2); // Convert to +628xxx
        }
        // If starts with 628, add 08 version
        elseif (substr($normalizedPhone, 0, 3) === '628') {
            $phoneFormats[] = $normalizedPhone; // Original 628xxx
            $phoneFormats[] = '08' . substr($normalizedPhone, 3); // Convert to 08xxx
            $phoneFormats[] = '+' . $normalizedPhone; // Add + prefix
        }
        // If starts with +628, add other versions
        elseif (substr($normalizedPhone, 0, 4) === '628' && substr($phone, 0, 1) === '+') {
            $phoneFormats[] = $normalizedPhone; // Without +
            $phoneFormats[] = '+' . $normalizedPhone; // With +
            $phoneFormats[] = '08' . substr($normalizedPhone, 3); // Convert to 08xxx
        }
        else {
            // For other formats, just use as is
            $phoneFormats[] = $normalizedPhone;
        }

        try {
            // Search for customers with matching phone numbers
            $customers = Customer::whereIn('no_hp', $phoneFormats)->get();
            
            if ($customers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ditemukan customer dengan nomor telepon tersebut',
                    'data' => []
                ], 404);
            }

            // Get customer IDs
            $customerIds = $customers->pluck('id_customer');

            // Find transactions for these customers - only show recent 2 months and limited info for security
            $twoMonthsAgo = now()->subMonths(2);
            
            $transactions = Transaksi::with([
                'customer:id_customer,nama,no_hp',
                'latestStatusChange:id_transaksi,changed_at,changed_by',
                'statusHistory:id_transaksi,status_lama,status_baru,changed_at,catatan_perubahan'
            ])
                ->whereIn('id_customer', $customerIds)
                ->where('created_at', '>=', $twoMonthsAgo) // Only recent 2 months
                ->select([
                    'id_transaksi',
                    'id_customer', 
                    'servis_layanan',
                    'merk',
                    'tipe',
                    'warna',
                    'tanggal_masuk',
                    'tanggal_keluar',
                    'status_transaksi',
                    'garansi',
                    'keluhan',
                    'kerusakan',
                    'teknisi',
                    'total_biaya',
                    'created_at',
                    'updated_at'
                ])
                ->orderBy('created_at', 'desc') // Most recent first
                ->get();

            // Add estimated completion days for each transaction
            $transactions = $transactions->map(function ($transaction) {
                $estimatedDays = null;
                $daysRemaining = null;
                
                // Check if repair is finished or has special status
                if (in_array($transaction->status_transaksi, ['Siap Diambil', 'Sudah Diambil', 'completed'])) {
                    $estimatedDays = 'SELESAI';
                    $daysRemaining = 0;
                } elseif ($transaction->status_transaksi === 'Gagal') {
                    $estimatedDays = 'Gagal, silahkan diambil';
                    $daysRemaining = 0;
                } elseif ($transaction->status_transaksi === 'Dibatalkan') {
                    $estimatedDays = 'Dibatalkan, silahkan diambil';
                    $daysRemaining = 0;
                } elseif ($transaction->tanggal_keluar) {
                    // Calculate days until completion for ongoing repairs
                    $keluarDate = Carbon::parse($transaction->tanggal_keluar);
                    $today = Carbon::now()->startOfDay();
                    $daysRemaining = $today->diffInDays($keluarDate, false); // false = can be negative
                    
                    if ($daysRemaining > 0) {
                        $estimatedDays = $daysRemaining . ' hari lagi';
                    } elseif ($daysRemaining == 0) {
                        $estimatedDays = 'Hari ini';
                    } else {
                        $estimatedDays = 'Terlambat ' . abs($daysRemaining) . ' hari';
                    }
                } else {
                    $estimatedDays = 'Belum ditentukan';
                }
                
                $transaction->estimated_completion = $estimatedDays;
                $transaction->days_remaining = $daysRemaining;
                
                // Add last status change timestamp
                if ($transaction->latestStatusChange) {
                    $transaction->last_status_changed = $transaction->latestStatusChange->changed_at;
                } else {
                    $transaction->last_status_changed = $transaction->updated_at;
                }
                
                return $transaction;
            });

            if ($transactions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer ditemukan, tetapi belum memiliki transaksi',
                    'data' => [
                        'customers' => $customers->makeHidden(['email', 'alamat', 'jenis_kelamin', 'status_pekerjaan', 'sumber', 'media_sosial']),
                        'transactions' => []
                    ]
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi ditemukan',
                'data' => [
                    'customers' => $customers->makeHidden(['email', 'alamat', 'jenis_kelamin', 'status_pekerjaan', 'sumber', 'media_sosial']),
                    'transactions' => $transactions
                ]
            ], 200);

        } catch (\Exception $e) {
            // Log error but don't expose internal details
            Log::error('Transaction tracking error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi nanti.',
                'data' => []
            ], 500);
        }
    }

    // Method to update status with custom notes
    public function updateStatusWithNotes(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'status_transaksi' => 'required|string|in:pending,in_progress,completed,cancelled,Diagnosa,Proses,Pengujian,Siap Diambil,Sudah Diambil,Gagal,Dibatalkan',
            'catatan_perubahan' => 'nullable|string|max:500'
        ]);

        try {
            $oldStatus = $transaksi->status_transaksi;
            $newStatus = $validated['status_transaksi'];

            // Update status
            $transaksi->status_transaksi = $newStatus;
            $transaksi->save();

            // The model event will automatically create the history record,
            // but we can add custom notes if provided
            if (isset($validated['catatan_perubahan']) && !empty($validated['catatan_perubahan'])) {
                // Update the latest history record with custom notes
                $latestHistory = TransaksiStatusHistory::where('id_transaksi', $id)
                    ->orderBy('changed_at', 'desc')
                    ->first();
                
                if ($latestHistory) {
                    $latestHistory->catatan_perubahan = $validated['catatan_perubahan'];
                    $latestHistory->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah dari "' . $oldStatus . '" ke "' . $newStatus . '"',
                'data' => [
                    'transaksi' => $transaksi,
                    'status_change' => [
                        'from' => $oldStatus,
                        'to' => $newStatus,
                        'changed_at' => now(),
                        'changed_by' => $user->id_karyawan,
                        'notes' => $validated['catatan_perubahan'] ?? null
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method to get status history for a transaction
    public function getStatusHistory($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $statusHistory = TransaksiStatusHistory::with('changedBy:id_karyawan,nama')
            ->where('id_transaksi', $id)
            ->orderBy('changed_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat status ditemukan',
            'data' => [
                'transaksi' => $transaksi,
                'status_history' => $statusHistory
            ]
        ]);
    }

    public function totalTransaksi()
    {
        $total = Transaksi::count();
        return response()->json([
            'message' => 'Total transaksi',
            'total_transaksi' => $total
        ], 200);
    }
}