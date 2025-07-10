<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function store(Request $request)
    {
        // 1️⃣ Validasi input dari frontend
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
}