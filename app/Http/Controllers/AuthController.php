<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'id_karyawan' => 'required',
            'password' => 'required'
        ]);

        // Cek apakah karyawan ada dan aktif
        $karyawan = Karyawan::where('id_karyawan', $credentials['id_karyawan'])->first();
        
        if (!$karyawan) {
            return back()->withErrors([
                'id_karyawan' => 'ID Karyawan tidak ditemukan.',
            ])->onlyInput('id_karyawan');
        }

        if (!$karyawan->isActive()) {
            return back()->withErrors([
                'id_karyawan' => 'Akun karyawan tidak aktif.',
            ])->onlyInput('id_karyawan');
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'id_karyawan' => 'ID Karyawan atau password salah.',
        ])->onlyInput('id_karyawan');
    }

    private function redirectBasedOnRole($user)
    {
        switch (strtolower($user->role)) {
            case 'superadmin':
                return redirect('/superadmin/dashboard');
            case 'admin':
                return redirect('/admin/dashboard');
            case 'teknisi':
                return redirect('/teknisi/dashboard');
            default:
                return redirect('/dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Berhasil logout');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        // Ambil user yang sedang login
        $user = Auth::user();

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        // Update password menggunakan query builder jika method update tidak work
        try {
            // Cara 1: Pakai Eloquent update
            Karyawan::where('id_karyawan', $user->id_karyawan)
                    ->update(['password' => Hash::make($request->new_password)]);
            
            // Logout setelah ganti password
            Auth::logout();
            $request->session()->invalidate();
            
            return redirect()->route('login')->with('success', 'Password berhasil diubah, silakan login kembali');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengubah password: ' . $e->getMessage()]);
        }
    }
}