<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;

class PengeluaranController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pengeluaran' => 'required|string|max:50',
            'jenis_pengeluaran' => 'required|string|max:30',
            'harga' => 'required|numeric|min:0',
            'kuantitas' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'lokasi' => 'nullable|string|max:100',
        ]);

        $pengeluaran = Pengeluaran::create($validated);

        return response()->json([
            'message' => 'Pengeluaran berhasil ditambahkan',
            'data' => $pengeluaran
        ], 201);
    }

    public function index()
    {
        $pengeluaran = Pengeluaran::all();

        return response()->json([
            'message' => 'Daftar pengeluaran',
            'data' => $pengeluaran
        ], 200);
    }

    public function show($id)
    {
        $pengeluaran = Pengeluaran::find($id);

        if (!$pengeluaran) {
            return response()->json([
                'message' => 'Pengeluaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail pengeluaran',
            'data' => $pengeluaran
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::find($id);

        if (!$pengeluaran) {
            return response()->json(['message' => 'Pengeluaran tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_pengeluaran' => 'string|max:50',
            'jenis_pengeluaran' => 'string|max:30',
            'harga' => 'numeric|min:0',
            'kuantitas' => 'integer|min:1',
            'tanggal' => 'date',
            'lokasi' => 'string|max:100',
        ]);

        $pengeluaran->update($validated);

        return response()->json($pengeluaran);
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::find($id);

        if (!$pengeluaran) {
            return response()->json(['message' => 'Pengeluaran tidak ditemukan'], 404);
        }

        $pengeluaran->delete();

        return response()->json(['message' => 'Pengeluaran berhasil dihapus']);
    }

}
