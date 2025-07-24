<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by id_karyawan
        $user = User::where('id_karyawan', $request->id_karyawan)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Id atau password salah.'], 401);
        }

        // Check if Sanctum is installed
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