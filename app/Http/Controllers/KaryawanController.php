<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan'           => 'required|string|min:5|unique:karyawan,id_karyawan',
            'nama'                  => 'required|string|max:50',
            'jenis_kelamin'         => 'required',
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

            if (isset($validated['tanggal_resign']) && $validated['tanggal_resign']) {
                $karyawan->status_karyawan = 'Resigned';
            } else {
                $karyawan->status_karyawan = 'Aktif';
            }
            $karyawan->save();

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
            'jenis_kelamin' => 'sometimes|required',
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
        $currentUser = Auth::guard('sanctum')->user();
        
        // Only superadmin can reset passwords
        if (!$currentUser || $currentUser->role !== 'superadmin') {
            return response()->json([
                'message' => 'Unauthorized. Only superadmin can reset passwords.'
            ], 403);
        }

        $user = User::where('id_karyawan', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'reset_type' => 'required|in:default,random',
            'new_password' => 'required_if:reset_type,manual|min:6'
        ]);

        $newPassword = '';
        
        if ($request->reset_type === 'default') {
            // Reset to default (id_karyawan)
            $newPassword = $id;
        } elseif ($request->reset_type === 'random') {
            // Generate random password
            $newPassword = $this->generateRandomPassword();
        } else {
            $newPassword = $request->new_password;
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return response()->json([
            'message' => 'Password reset successful',
            'new_password' => $newPassword // Return the new password for superadmin to give to user
        ]);
    }

    public function changePassword(Request $request)
    {
        $currentUser = Auth::guard('sanctum')->user();
        
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        // Get the user from the User model using id_karyawan
        $user = User::where('id_karyawan', $currentUser->id_karyawan)->first();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Verify current password
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }

    private function generateRandomPassword($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
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