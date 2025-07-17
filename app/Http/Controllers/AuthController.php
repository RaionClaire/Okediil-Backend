<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;



class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|string',
            'password' => 'required|string',
        ]);

        $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan)->first();

        if (!$karyawan || !Hash::check($request->password, $karyawan->password)) {
            throw ValidationException::withMessages([
                'id_karyawan' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Hapus token lama
        $karyawan->tokens()->delete();

        // Buat token baru
        $token = $karyawan->createToken('auth-token')->plainTextToken;

        Log::info('Hello, Laravel logging works!');

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $karyawan,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        Log::info("uda sampe sini");
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out']);
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
}

        Log::info('User logged out: ' . $request->user()->id_karyawan);
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }
}