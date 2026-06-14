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

        if ($user->role === 'recruiter' && !$user->is_verified) {
            return back()->withErrors(['login' => 'Akun rekruter Anda sedang menunggu verifikasi oleh Admin.']);
        }

        session([
            'user_id'  => $user->id,
            'nama'     => $user->nama,
            'username' => $user->username,
            'role'     => $user->role,
        ]);

        return match($user->role) {
            'admin'     => redirect('/admin/dashboard'),
            'recruiter' => redirect('/recruiter/dashboard'),
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

    public function checkEmail(Request $request)
    {
        $email = trim($request->input('email', ''));
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar.']);
        }
        return response()->json(['status' => 'success', 'message' => 'Email ditemukan.']);
    }

    public function resetPassword(Request $request)
    {
        $email = trim($request->input('email', ''));
        $password = $request->input('password', '');
        $confirm = $request->input('confirm_password', '');

        if (strlen($password) < 8) {
            return response()->json(['status' => 'error', 'message' => 'Password minimal 8 karakter.']);
        }
        if ($password !== $confirm) {
            return response()->json(['status' => 'error', 'message' => 'Konfirmasi password tidak cocok.']);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar.']);
        }

        $user->password = Hash::make($password);
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diubah. Silakan login.']);
    }

    public function showRegisterRecruiter()
    {
        return view('auth.regis_recruiter');
    }

    public function registerRecruiter(Request $request)
    {
        $nama = trim($request->input('nama', ''));
        $username = trim($request->input('username', ''));
        $email = trim($request->input('email', ''));
        $telepon = trim($request->input('telepon', ''));
        $password = $request->input('password', '');
        $confirm = $request->input('confirm_password', '');
        
        $nama_perusahaan = trim($request->input('nama_perusahaan', ''));
        $tipe_bisnis = trim($request->input('tipe_bisnis', ''));
        $lokasi = trim($request->input('lokasi', ''));
        $website = trim($request->input('website', ''));
        $ditemukan_tahun = trim($request->input('ditemukan_tahun', ''));
        $deskripsi = trim($request->input('deskripsi', ''));

        if (!$nama || !$username || !$email || !$password || !$confirm || !$nama_perusahaan) {
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

        // Insert Recruiter
        DB::insert('
            INSERT INTO users (nama, username, email, password, telepon, role, is_verified)
            VALUES (?, ?, ?, ?, ?, \'recruiter\', 0)
        ', [$nama, $username, $email, Hash::make($password), $telepon]);

        // Get the inserted user ID
        $user = DB::selectOne('SELECT id FROM users WHERE email = ?', [$email]);

        // Insert Perusahaan
        DB::insert('
            INSERT INTO perusahaan (recruiter_id, nama, lokasi, website, tipe_bisnis, ditemukan_tahun, deskripsi)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ', [$user->id, $nama_perusahaan, $lokasi, $website, $tipe_bisnis, $ditemukan_tahun ? intval($ditemukan_tahun) : null, $deskripsi]);

        return view('auth.regis_recruiter_success');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}