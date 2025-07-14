<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan'           => 'required|string|size:6|unique:karyawan,id_karyawan',
            'nama'                  => 'required|string|max:50',
            'jenis_kelamin'         => 'required|in:L,P',
            'tempat_tanggal_lahir'  => 'required|string|max:50',
            'alamat'                => 'required|string|max:150',
            'no_hp'                 => 'required|string|max:15',
            'tanggal_masuk'         => 'required|date',
            'bidang'                => 'nullable|string|max:20',
            'status_karyawan'       => 'nullable|string|max:20',
            'cabang'                => 'nullable|string|max:20',
            'ukuran_baju'           => 'nullable|string|max:5',
            'tanggal_resign'        => 'nullable|date',
            'role'                  => 'required|in:admin,superadmin,teknisi',
            'password'              => 'required|string|min:6',
        ]);

        $karyawan = Karyawan::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Karyawan berhasil ditambahkan',
            'data'    => $karyawan
        ], 201);
    }

    public function index(){
        $karyawans = Karyawan::all();

        return response()->json([ $karyawans ], 200);
    }

    public function show($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        return response()->json($karyawan, 200);
    }

    public function update(Request $request, $id)
{
    $karyawan = Karyawan::find($id);

    if (!$karyawan) {
        return response()->json(['message' => 'Karyawan not found'], 404);
    }

    $karyawan->update($request->all());

    return response()->json(['message' => 'Karyawan updated', 'data' => $karyawan]);
}

    public function destroy($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        $karyawan->delete();

        return response()->json(['message' => 'Karyawan berhasil dihapus'], 200);
    }

    public function resetPassword(Request $request, $id)
{
    $karyawan = Karyawan::find($id);

    if (!$karyawan) {
        return response()->json(['message' => 'Karyawan not found'], 404);
    }

    $request->validate([
        'new_password' => 'required|min:6'
    ]);

    $karyawan->password = Hash::make($request->new_password);
    $karyawan->save();

    return response()->json(['message' => 'Password reset successful']);
}

    public function filter(Request $request)
{
    $query = Karyawan::query();

    if ($request->has('role')) {
        $query->where('role', $request->role);
    }

    if ($request->has('status_karyawan')) {
        $query->where('status_karyawan', $request->status_karyawan);
    }

    $filtered = $query->get();

    return response()->json($filtered);
}


}



