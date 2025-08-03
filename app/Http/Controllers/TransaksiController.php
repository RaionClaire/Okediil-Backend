<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Customer;

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
        'pembelian_ids' => 'nullable|array', // Array of pembelian IDs
        'pembelian_ids.*' => 'exists:pembelian,id_pembelian',
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
        // Buat transaksi
        $transaksi = Transaksi::create($validated);

        // Create cart entries if pembelian_ids are provided
        if (isset($validated['pembelian_ids']) && is_array($validated['pembelian_ids'])) {
            foreach ($validated['pembelian_ids'] as $pembelianId) {
                \App\Models\Cart::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_pembelian' => $pembelianId
                ]);

                // Update pembelian status to 0 (used)
                $pembelian = \App\Models\Pembelian::find($pembelianId);
                if ($pembelian) {
                    $pembelian->status = 0;
                    $pembelian->save();
                }
            }
        }

        // Note: berapa_kali_servis is now calculated dynamically in Customer model
        // No need to manually increment the counter

        // Load relationships for response
        $transaksi->load(['customer', 'karyawan', 'cartItems.pembelian']);

        return response()->json([
            'message' => 'Transaksi berhasil ditambahkan',
            'id_transaksi' => $transaksi->id_transaksi,
            'data' => $transaksi,
            'user' => $user->nama,
        ], 201);

    } catch (\Exception $e) {
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

        $transaksi = Transaksi::with(['customer', 'karyawan', 'cartItems.pembelian'])->get();
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
            'pembelian_ids' => 'nullable|array', // Array of pembelian IDs
            'pembelian_ids.*' => 'exists:pembelian,id_pembelian',
        ]);

        // Update transaksi
        $transaksi->update($validated);

        // Handle cart items if pembelian_ids provided
        if (isset($validated['pembelian_ids'])) {
            // Get current cart items
            $currentCartItems = \App\Models\Cart::where('id_transaksi', $id)->get();
            
            // Reset status of previously used pembelian items
            foreach ($currentCartItems as $cartItem) {
                $pembelian = \App\Models\Pembelian::find($cartItem->id_pembelian);
                if ($pembelian) {
                    $pembelian->status = 1; // Available again
                    $pembelian->save();
                }
            }

            // Delete old cart items
            \App\Models\Cart::where('id_transaksi', $id)->delete();

            // Create new cart items
            foreach ($validated['pembelian_ids'] as $pembelianId) {
                \App\Models\Cart::create([
                    'id_transaksi' => $id,
                    'id_pembelian' => $pembelianId
                ]);

                // Update pembelian status to 0 (used)
                $pembelian = \App\Models\Pembelian::find($pembelianId);
                if ($pembelian) {
                    $pembelian->status = 0;
                    $pembelian->save();
                }
            }
        }

        return response()->json(['message' => 'Transaksi berhasil diperbarui']);
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

        // Get current cart items and reset pembelian status
        $currentCartItems = \App\Models\Cart::where('id_transaksi', $id)->get();
        foreach ($currentCartItems as $cartItem) {
            $pembelian = \App\Models\Pembelian::find($cartItem->id_pembelian);
            if ($pembelian) {
                $pembelian->status = 1; // Available again
                $pembelian->save();
            }
        }

        // Delete cart items first
        \App\Models\Cart::where('id_transaksi', $id)->delete();

        // Delete transaksi
        $transaksi->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus']);
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

    public function totalTransaksi()
    {
        $total = Transaksi::count();
        return response()->json([
            'message' => 'Total transaksi',
            'total_transaksi' => $total
        ], 200);
    }

    public function syncCustomerServiceCounts()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            Customer::syncAllServiceCounts();
            return response()->json([
                'message' => 'Customer service counts synchronized successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error synchronizing customer service counts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCustomerServiceCounts()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $customers = Customer::with('transaksi')->get()->map(function ($customer) {
                return [
                    'id_customer' => $customer->id_customer,
                    'nama' => $customer->nama,
                    'stored_count' => $customer->getOriginal('berapa_kali_servis') ?? 0,
                    'actual_count' => $customer->getActualServiceCount(),
                    'dynamic_count' => $customer->berapa_kali_servis, // Uses accessor
                ];
            });

            return response()->json([
                'message' => 'Customer service counts retrieved successfully',
                'data' => $customers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving customer service counts',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}