<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Customer;
use App\Models\Cart;

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
        'pembelian_items' => 'required|array|min:1',
        'pembelian_items.*' => 'required|exists:pembelian,id_pembelian',
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

        // Create cart entries for each pembelian item
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

    public function totalTransaksi()
    {
        $total = Transaksi::count();
        return response()->json([
            'message' => 'Total transaksi',
            'total_transaksi' => $total
        ], 200);
    }
}