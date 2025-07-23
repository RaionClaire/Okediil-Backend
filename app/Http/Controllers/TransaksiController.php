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
    ]);

    $validated['id_karyawan'] = $user->id_karyawan;

    try {
        // Buat transaksi
        $transaksi = Transaksi::create($validated);

        // Update customer counter
        $customer = Customer::find($validated['id_customer']);
        if ($customer) {
            $customer->berapa_kali_servis += 1;
            $customer->save();
        }

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

        $transaksi = Transaksi::with(['customer', 'karyawan'])->get();
        return response()->json($transaksi);
    }

    public function show($id) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::with(['customer', 'karyawan'])->find($id);
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
            'kuantitas' => 'required|integer|min:1',
            'total_biaya' => 'required|numeric',
            'status_transaksi' => 'required|string',
        ]);

        $transaksi->update($validated);
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

        $transaksi->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }

    public function filter(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $query = Transaksi::with(['customer', 'karyawan']);

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
}