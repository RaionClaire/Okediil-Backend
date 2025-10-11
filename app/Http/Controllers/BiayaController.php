<?php

namespace App\Http\Controllers;

use App\Models\Biaya;
use Illuminate\Http\Request;

class BiayaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_biaya' => 'required|string|max:50',
            'biaya' => 'required|numeric',
            'jenis_biaya' => 'required|string|max:50',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string|max:100',
        ]);

        $biaya = Biaya::create($validated);

        return response()->json([
            'message' => 'Biaya berhasil ditambahkan',
            'data' => $biaya
        ], 201);
    }

    public function totalBiaya()
    {
        $total = Biaya::sum('biaya');

        return response()->json([
            'message' => 'Total biaya',
            'total_biaya' => $total
        ]);
    }

    public function index()
    {
        $biayas = Biaya::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Daftar biaya',
            'data' => $biayas
        ], 200);
    }

    public function show($id)
    {
        $biaya = Biaya::find($id);

        if (!$biaya) {
            return response()->json([
                'message' => 'Biaya tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail biaya',
            'data' => $biaya
        ], 200);
    }

    public function destroy($id)
    {
        $biaya = Biaya::find($id);

        if (!$biaya) {
            return response()->json([
                'message' => 'Biaya tidak ditemukan'
            ], 404);
        }

        $biaya->delete();

        return response()->json([
            'message' => 'Biaya berhasil dihapus'
        ], 200);
    }

    function update(Request $request, $id)
    {
        $biaya = Biaya::find($id);

        if (!$biaya) {
            return response()->json([
                'message' => 'Biaya tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_biaya' => 'required|string|max:50',
            'biaya' => 'required|numeric',
            'jenis_biaya' => 'required|string|max:50',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string|max:100',
        ]);

        $biaya->update($validated);

        return response()->json([
            'message' => 'Biaya berhasil diperbarui',
            'data' => $biaya
        ], 200);
    }
}
