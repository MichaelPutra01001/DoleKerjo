<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

    public function showRegister()
{
    return view('auth.regis');
}

public function register(Request $request)
{
    $nama      = trim($request->input('nama', ''));
    $username  = trim($request->input('username', ''));
    $email     = trim($request->input('email', ''));
    $telepon   = trim($request->input('telepon', ''));
    $lokasi    = trim($request->input('lokasi', ''));
    $password  = $request->input('password', '');
    $confirm   = $request->input('confirm_password', '');
    $pendidikan = $request->input('pendidikan', '');
    $jurusan   = trim($request->input('jurusan', ''));

    if (!$nama || !$username || !$email || !$password || !$confirm) {
        return back()->withErrors(['Harap lengkapi semua kolom yang wajib diisi.'])->withInput();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return back()->withErrors(['Format email tidak valid.'])->withInput();
    }
    if (strlen($password) < 8) {
        return back()->withErrors(['Password minimal 8 karakter.'])->withInput();
    }
    if ($password !== $confirm) {
        return back()->withErrors(['Konfirmasi password tidak cocok.'])->withInput();
    }

    $existing = DB::selectOne('SELECT id FROM users WHERE email = ? OR username = ?', [$email, $username]);
    if ($existing) {
        return back()->withErrors(['Email atau username sudah terdaftar.'])->withInput();
    }

    DB::insert('
        INSERT INTO users (nama, username, email, password, telepon, lokasi, pendidikan, jurusan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ', [$nama, $username, $email, Hash::make($password), $telepon, $lokasi, $pendidikan, $jurusan]);

    $user = DB::selectOne('SELECT * FROM users WHERE email = ?', [$email]);

    session([
        'user_id'  => $user->id,
        'nama'     => $user->nama,
        'username' => $user->username,
        'role'     => $user->role,
    ]);

    return redirect()->route('home');
}

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}