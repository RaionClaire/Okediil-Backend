<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Transaksi;
use App\Models\Customer;
use App\Models\Pembelian;


class TransaksiController extends Controller
{


public function store(Request $request) {

    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $validated = $request->validate([
        'id_customer' => 'required|exists:customers,id_customer',
        'servis_layanan' => 'required|string',
        'merk' => 'required|string',
        'tipe' => 'required|string',
        'warna' => 'required|string',
        'tanggal_masuk' => 'required|date',
        'id_pembelian' => 'nullable|exists:pembelian,id_pembelian',
        'kuantitas' => 'required|integer|min:1',
        'total_biaya' => 'required|numeric',
        'status_transaksi' => 'required|string',
    ]);

    $validated['id_karyawan'] = Auth::user()->id_karyawan;

    DB::transaction(function () use ($validated) {
        $transaksi = Transaksi::create($validated);

        $customer = Customer::find($validated['id_customer']);
        $customer->berapa_kali_servis += 1;
        $customer->save();

        if (!empty($validated['id_pembelian'])) {
            $pembelian = Pembelian::find($validated['id_pembelian']);
            $pembelian->jumlah_produk -= $validated['kuantitas'];
            if ($pembelian->jumlah_produk <= 0) {
                $pembelian->status = 'HABIS';
            }
            $pembelian->save();
        }
    });

    return response()->json(['message' => 'Transaksi berhasil ditambahkan']);
}

    public function index() {
        $transaksi = Transaksi::with(['customer', 'karyawan', 'pembelian'])->get();
        return response()->json($transaksi);
    }

    public function show($id) {
        $transaksi = Transaksi::with(['customer', 'karyawan', 'pembelian'])->find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }
        return response()->json($transaksi);
    }

    public function update(Request $request, $id) {
        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'id_customer' => 'required|exists:customers,id_customer',
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'servis_layanan' => 'required|string',
            'merk' => 'required|string',
            'tipe' => 'required|string',
            'warna' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'id_pembelian' => 'nullable|exists:pembelians,id_pembelian',
            'kuantitas' => 'required|integer|min:1',
            'total_biaya' => 'required|numeric',
            'status_transaksi' => 'required|string',
        ]);

        $transaksi->update($validated);
        return response()->json(['message' => 'Transaksi berhasil diperbarui']);
    }

    public function destroy($id) {
        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $transaksi->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }

    public function filter()
    {
        $query = Transaksi::query();

        if (request()->has('year')) {
            $query->whereYear('tanggal_masuk', request()->year);
        }

        if (request()->has('month')) {
            $query->whereMonth('tanggal_masuk', request()->month);
        }

        return response()->json($query->get(), 200);

    }


}




