<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'id_karyawan' => 'required|string',
            'password' => 'required|string',
        ]);

        $karyawan = Karyawan::where('id_karyawan', $fields['id_karyawan'])->first();

        if (!$karyawan || !Hash::check($fields['password'], $karyawan->password)) {
            Log::warning('Failed login attempt - user not found: ' . $fields['id_karyawan']);
            throw ValidationException::withMessages([
                'id_karyawan' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Optional: delete old tokens
        $karyawan->tokens()->delete();

        // Create new token
        $token = $karyawan->createToken('auth-token')->plainTextToken;

        Log::info('User logged in: ' . $karyawan->id_karyawan);

        return response()->json([
            'message' => 'Login successful',
            'user' => $karyawan,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {

        Log::debug('Current user:', ['user' => Auth::user()]);

//         $karyawan = Auth::User();

//         if ($karyawan) {
//             $karyawan->tokens()->delete();
//             Log::info('User logged out: ' . $karyawan->id_karyawan);
//             return response()->json(['message' => 'Logout successful'], 200);
//         }

//         return response()->json(['message' => 'No user logged in'], 401);
    }
}
   