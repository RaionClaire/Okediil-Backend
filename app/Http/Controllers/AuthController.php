<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $user = Karyawan::where($request->id_karyawan);

$user = Karyawan::where('id_karyawan', (string)$request->id_karyawan)->first();


            if (!$user) {
                return response()->json(['message' => 'Login gagal karena ID atau password salah'], 401);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Login gagal karena ID atau password salah'], 401);
            }

            // Delete existing tokens for this user (optional)
            $user->tokens()->delete();

            // Create new token
            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => [
                    'id_karyawan' => $user->id_karyawan,
                    'nama' => $user->nama,                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout berhasil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }
}