<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan'           => 'required|string|min:5|unique:karyawan,id_karyawan',
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
        ]);

        DB::beginTransaction();
        
        try {
            $karyawanData = $validated;
            unset($karyawanData['password']); // Remove password from karyawan data
            
            $karyawan = Karyawan::create($karyawanData);

            if ($validated['tanggal_resign']) {
                $karyawan->status_karyawan = 'Resigned';
            } else {
                $karyawan->status_karyawan = 'Aktif';
            }

            User::create([
                'id_karyawan' => $validated['id_karyawan'],
                'nama' => $validated['nama'],
                'password' => Hash::make($validated['id_karyawan']),
                'role' => $validated['role'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Karyawan berhasil ditambahkan',
                'data' => $karyawan
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal menambahkan karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $karyawans = Karyawan::all();
        return response()->json($karyawans, 200);
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

        $validated = $request->validate([
            'nama' => 'sometimes|string|max:50',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'tempat_tanggal_lahir' => 'sometimes|string|max:50',
            'alamat' => 'sometimes|string|max:150',
            'no_hp' => 'sometimes|string|max:15',
            'tanggal_masuk' => 'sometimes|date',
            'bidang' => 'nullable|string|max:20',
            'status_karyawan' => 'nullable|string|max:20',
            'cabang' => 'nullable|string|max:20',
            'ukuran_baju' => 'nullable|string|max:5',
            'tanggal_resign' => 'nullable|date',
            'role' => 'sometimes|in:admin,superadmin,teknisi',
        ]);

        DB::beginTransaction();
        
        try {
            // Update karyawan
            $karyawan->update($validated);

            // Update user if nama or role changed
            if (isset($validated['nama']) || isset($validated['role'])) {
                $userUpdateData = [];
                if (isset($validated['nama'])) {
                    $userUpdateData['nama'] = $validated['nama'];
                }
                if (isset($validated['role'])) {
                    $userUpdateData['role'] = $validated['role'];
                }
                
                User::where('id_karyawan', $id)->update($userUpdateData);
            }

            DB::commit();

            return response()->json([
                'message' => 'Karyawan updated successfully',
                'data' => $karyawan->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to update karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        
        try {
            User::where('id_karyawan', $id)->delete();
            
            $karyawan->delete();

            DB::commit();

            return response()->json(['message' => 'Karyawan berhasil dihapus'], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to delete karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::where('id_karyawan', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'new_password' => 'required|min:6'
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

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

    public function totalKaryawan()
    {
        $total = Karyawan::count();
        return response()->json(['total' => $total]);
    }
}