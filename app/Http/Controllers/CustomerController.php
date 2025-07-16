<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer; 
class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'no_hp' => 'required|string|max:17',
            'alamat' => 'required|string|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'status_pekerjaan' => 'required|string|max:10',
            'sumber' => 'required|string|max:15',
            'media_sosial' => 'nullable|string|max:20',
            'berapa_kali_servis' => 'nullable|integer|min:0',
        ]);

        $no_hp = preg_replace('/\D/', '', $request -> no_hp);
        $id_customer = substr($no_hp, -6);
        $customer = Customer::create([
            'id_customer' => $id_customer,
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $no_hp,
            'alamat' => $validated['alamat'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'status_pekerjaan' => $validated['status_pekerjaan'],
            'sumber' => $validated['sumber'],
            'media_sosial' => $validated['media_sosial'],
            // 'berapa_kali_servis' => $validated['berapa_kali_servis'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Customer berhasil ditambahkan',
            'data' => $customer
        ], 201);
    }

    public function index()
    {
        $customers = Customer::all();

        return response()->json([
            'message' => 'Daftar customer',
            'data' => $customers
        ], 200);
    }

    public function show($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'message' => 'Customer tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail customer',
            'data' => $customer
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'message' => 'Customer tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:50',
            'email' => 'nullable|email|max:100',
            'no_hp' => 'sometimes|required|string|max:17',
            'alamat' => 'sometimes|required|string|max:150',
            'jenis_kelamin' => 'sometimes|required|in:L,P',
            'status_pekerjaan' => 'sometimes|required|string|max:10',
            'sumber' => 'sometimes|required|string|max:15',
            'media_sosial' => 'nullable|string|max:20',
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => 'Customer berhasil diperbarui',
            'data' => $customer
        ], 200);
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'message' => 'Customer tidak ditemukan',
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'message' => 'Customer berhasil dihapus',
        ], 200);
    }

    public function filter(Request $request)
    {
        $query = Customer::query();

        if ($request->has('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->has('status_pekerjaan')) {
            $query->where('status_pekerjaan', $request->status_pekerjaan);
        }

        if ($request->has('sumber')) {
            $query->where('sumber', $request->sumber);
        }

        $customers = $query->get();

        return response()->json([
            'message' => 'Hasil filter customer',
            'data' => $customers
        ], 200);
    }


}
