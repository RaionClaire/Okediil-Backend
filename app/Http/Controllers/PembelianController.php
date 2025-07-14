<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;

class PembelianController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:50',
            'kategori_produk' => 'required|string|max:30',
            'merk' => 'required|string|max:30',
            'jenis_produk' => 'required|string|max:30',
            'tanggal' => 'required|date',
            'jumlah_produk' => 'required|integer|min:1',
            'kualitas_produk' => 'required|string|max:20',
            'garansi_produk' => 'nullable|date',
            'nama_mitra' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:20',
            'status' => 'required|string|max:20',
        ]);

        $jumlah = $validated['jumlah_produk'];
        $validated['jumlah_produk']=1;

        for ($i = 0; $i < $jumlah; $i++) {
            if ($i == 1) {
                $validated['ongkir'] = 0;
            }

            $pembelian = Pembelian::create($validated);
        }

        return response()->json([
            'message' => "$jumlah Pembelian berhasil ditambahkan",
            'data' => $pembelian
        ], 201);
    }


    public function index()
    {
        $pembelians = Pembelian::all();

        return response()->json([
            'message' => 'Daftar pembelian',
            'data' => $pembelians
        ], 200);
    }

    public function show($id)
    {
        $pembelian = Pembelian::find($id);

        if (!$pembelian) {
            return response()->json([
                'message' => 'Pembelian tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail pembelian',
            'data' => $pembelian
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $pembelian = Pembelian::find($id);

        if (!$pembelian) {
            return response()->json([
                'message' => 'Pembelian tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_produk' => 'string|max:50',
            'kategori_produk' => 'string|max:30',
            'merk' => 'string|max:30',
            'jenis_produk' => 'string|max:30',
            'tanggal' => 'date',
            'jumlah_produk' => 'integer|min:1',
            'kualitas_produk' => 'string|max:20',
            'garansi_produk' => 'date',
            'nama_mitra' => 'string|max:50',
            'harga_beli' => 'numeric|min:0',
            'ongkir' => 'numeric|min:0',
            'metode_pembayaran' => 'string|max:20',
            'status' => 'string|max:20',
        ]);

        $pembelian->update($validated);

        return response()->json(['message' => 'Pembelian berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $pembelian = Pembelian::find($id);

        if (!$pembelian) {
            return response()->json(['message' => 'Pembelian tidak ditemukan'], 404);
        }

        $pembelian->delete();

        return response()->json(['message' => 'Pembelian berhasil dihapus']);
    }


    public function filter(Request $request)
    {
        $query = Pembelian::query();

        if ($request->has('nama_produk')) {
            $query->where('nama_produk', 'like', '%' . $request->nama_produk . '%');
        }

        if ($request->has('kategori_produk')) {
            $query->where('kategori_produk', $request->kategori_produk);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $pembelians = $query->get();

        return response()->json([
            'message' => 'Daftar pembelian yang difilter',
            'data' => $pembelians
        ], 200);
    }
    
}
