<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Email dan password tidak boleh kosong.',
            'password.required' => 'Email dan password tidak boleh kosong.',
        ]);

        $identifier = trim($request->username);

        $user = User::where('email', $identifier)
                    ->orWhere('username', $identifier)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Email atau password salah.']);
        }

        session([
            'user_id'  => $user->id,
            'nama'     => $user->nama,
            'username' => $user->username,
            'role'     => $user->role,
        ]);

        return match($user->role) {
            'admin'     => redirect('/admin/jobs'),
            'recruiter' => redirect('/recruiter/jobs'),
            default     => redirect('/home'),
        };
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}