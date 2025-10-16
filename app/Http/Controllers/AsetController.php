<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aset; 
use Illuminate\Support\Facades\Log;

class AsetController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:50',
            'jenis_aset' => 'required|string|max:30',
            'kondisi' => 'nullable|string|max:30',
            'tanggal_pembelian' => 'nullable|date',
            'harga' => 'required|numeric|min:0',
            'lokasi' => 'nullable|string|max:100',
            'garansi' => 'nullable|date',
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $jumlah = $validated['jumlah'];
        $validated['jumlah']=1;
        
        for ($i = 0; $i < $jumlah; $i++) {
            $aset = Aset::create($validated);
        }

        return response()->json([
            'message' => "$jumlah Aset berhasil ditambahkan",
            'data' => $aset
        ], 201);
    }

    public function index()
    {
        $asets = Aset::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Daftar aset',
            'data' => $asets
        ], 200);
    }

    public function show($id)
    {
        $aset = Aset::find($id);

        if (!$aset) {
            return response()->json([
                'message' => 'Aset tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail aset',
            'data' => $aset
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $aset = Aset::find($id);

        if (!$aset) {
            return response()->json([
                'message' => 'Aset tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_aset' => 'sometimes|required|string|max:50',
            'jumlah' => 'sometimes|required|integer|min:1',
            'harga_satuan' => 'sometimes|required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:100',
            'kondisi' => 'nullable|string|max:20',
            'tanggal_pembelian' => 'nullable|date',
            'harga' => 'sometimes|required|numeric|min:0',
            'garansi' => 'nullable|date',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $aset->update($validated);

        return response()->json([
            'message' => 'Aset berhasil diperbarui',
            'data' => $aset
        ], 200);
    }

    public function destroy($id)
    {
        $aset = Aset::find($id);

        if (!$aset) {
            return response()->json([
                'message' => 'Aset tidak ditemukan'
            ], 404);
        }

        $aset->delete();

        return response()->json([
            'message' => 'Aset berhasil dihapus'
        ], 200);
    }

    public function filter(Request $request)
    {
        $query = Aset::query();

        if ($request->has('jenis_aset')) {
            $query->where('jenis_aset', $request->input('jenis_aset'));
        }

        if ($request->has('kondisi')) {
            $query->where('kondisi', $request->input('kondisi'));
        }

        if ($request->has('lokasi')) {
            $query->where('lokasi', 'like', '%' . $request->input('lokasi') . '%');
        }

        $asets = $query->get();

        return response()->json([
            'message' => 'Hasil filter aset',
            'data' => $asets
        ], 200);
    }

    public function totalNominalAset()
    {
        $total = Aset::sum('harga');

        return response()->json([
            'message' => 'Total nominal aset',
            'data' => ['total' => $total]
        ], 200);
    }


    public function showPublic($id)
    {
        $aset = Aset::find($id);

        if (!$aset) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $aset
        ]);
    }

}
