<?php

namespace App\Http\Controllers;
use App\Models\Crm;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'tanggal' => 'required|date',
            'alamat' => 'required|string|max:150',
            'no_hp' => 'required|string|max:17',
            'jenis_kelamin' => 'required',
            'pekerjaan' => 'required|string|max:50',
            'sumber_chat' => 'required|string|max:20',
            'jenis_produk' => 'nullable|string|max:30',
            'kondisi' => 'nullable|string|max:20',
            'merk' => 'nullable|string|max:30',
            'tipe_produk' => 'nullable|string|max:30',
            'status' => 'required|string|max:20',
        ]);

        $crm = Crm::create($validated);

        return response()->json([
            'message' => 'CRM entry successfully created',
            'data' => $crm
        ], 201);
    }

    public function index()
    {
        $crmEntries = Crm::all();

        return response()->json([
            'message' => 'List of CRM entries',
            'data' => $crmEntries
        ], 200);
    }

    public function show($id)
    {
        $crmEntry = Crm::find($id);

        if (!$crmEntry) {
            return response()->json([
                'message' => 'CRM entry not found'
            ], 404);
        }

        return response()->json([
            'message' => 'CRM entry details',
            'data' => $crmEntry
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $crmEntry = Crm::find($id);

        if (!$crmEntry) {
            return response()->json([
                'message' => 'CRM entry not found'
            ], 404);
        }

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:50',
            'tanggal' => 'sometimes|required|date',
            'alamat' => 'sometimes|required|string|max:150',
            'no_hp' => 'sometimes|required|string|max:17',
            'jenis_kelamin' => 'sometimes|required',
            'pekerjaan' => 'sometimes|required|string|max:50',
            'sumber_chat' => 'sometimes|required|string|max:20',
            'jenis_produk' => 'nullable|string|max:30',
            'kondisi' => 'nullable|string|max:20',
            'merk' => 'nullable|string|max:30',
            'tipe_produk' => 'nullable|string|max:30',
            'status' => 'sometimes|required|string|max:20',
        ]);

        $crmEntry->update($validated);

        return response()->json([
            'message' => 'CRM entry successfully updated',
            'data' => $crmEntry
        ], 200);
    }

    public function destroy($id)
    {
        $crmEntry = Crm::find($id);

        if (!$crmEntry) {
            return response()->json([
                'message' => 'CRM entry not found'
            ], 404);
        }

        $crmEntry->delete();

        return response()->json([
            'message' => 'CRM entry successfully deleted'
        ], 200);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $crmEntries = Crm::where('nama', 'like', "%{$query}%")
            ->orWhere('alamat', 'like', "%{$query}%")
            ->orWhere('no_hp', 'like', "%{$query}%")
            ->get();

        return response()->json([
            'message' => 'Search results',
            'data' => $crmEntries
        ], 200);
    }
}
