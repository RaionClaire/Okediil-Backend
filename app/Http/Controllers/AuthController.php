<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('id_karyawan', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!method_exists($user, 'createToken')) {
            return response()->json([
                'message' => 'Token creation not available. Please install Laravel Sanctum.',
            ], 500);
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        
        // Load karyawan relationship to get full employee data
        $user->load('karyawan');
        
        return response()->json($user);
    }
}