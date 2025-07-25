<?php

namespace App\Http\Controllers;

use App\Models\Omal;
use Illuminate\Http\Request;

class OmalController extends Controller
{
        public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'status_omal' => 'required|string|max:20',
            'keterangan' => 'required|string|max:1000',
            'harga' => 'required|numeric',
        ]);

        $omal = Omal::create($validated);
        return response()->json($omal, 201);
    }


    public function index()
    {
        $omals = Omal::all();
        return response()->json($omals);
    }

    public function show($id)
    {
        $omal = Omal::find($id);
        if (!$omal) {
            return response()->json(['message' => 'Omal not found'], 404);
        }
        return response()->json($omal);
    }

    public function update(Request $request, $id)
    {
        $omal = Omal::find($id);
        if (!$omal) {
            return response()->json(['message' => 'Omal not found'], 404);
        }

        $validated = $request->validate([
            'tanggal' => 'date',
            'status_omal' => 'string|max:20',
            'keterangan' => 'string|max:1000',
            'harga' => 'numeric',
        ]);

        $omal->update($validated);
        return response()->json($omal);
    }

    public function destroy($id)
    {
        $omal = Omal::find($id);
        if (!$omal) {
            return response()->json(['message' => 'Omal not found'], 404);
        }

        $omal->delete();
        return response()->json(['message' => 'Omal deleted']);
    }

    public function filter(Request $request)
    {
        $status = $request->query('status');
        if (!$status) {
            return response()->json(['message' => 'Status query parameter is required'], 400);
        }

        $omals = Omal::where('status_omal', $status)->get();
        return response()->json($omals);
    }

    public function totalNominalOmal()
    {
        $total = Omal::sum('harga');
        return response()->json(['total' => $total]);
    }
}
