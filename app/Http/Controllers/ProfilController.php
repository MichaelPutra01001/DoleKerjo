<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    // Cek login helper
    private function cekLogin()
    {
        if (!session('user_id')) {
            abort(401);
        }
    }

    // Tampilkan halaman profil
    public function index()
    {
        $this->cekLogin();
        return view('profil');
    }

    // Ambil data profil (JSON) — ganti Profil.php
    public function getData()
    {
        $this->cekLogin();
        $user_id = session('user_id');

        $user = DB::selectOne('
            SELECT id, nama, username, email, telepon, lokasi, bio,
                   pendidikan, jurusan, tanggal_lahir, gender, foto_profil, created_at
            FROM users WHERE id = ?
        ', [$user_id]);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan.'], 404);
        }

        $skills = DB::select('
            SELECT s.nama, s.kategori, us.level
            FROM user_skills us
            JOIN skills s ON s.id = us.skill_id
            WHERE us.user_id = ?
        ', [$user_id]);

        $lamaran = DB::select('
            SELECT l.status, l.created_at, j.nama_posisi, j.nama_perusahaan
            FROM lamaran l
            JOIN jobs j ON j.id = l.job_id
            WHERE l.user_id = ?
            ORDER BY l.created_at DESC
        ', [$user_id]);

        return response()->json([
            'status'  => 'success',
            'user'    => $user,
            'skills'  => $skills,
            'lamaran' => $lamaran,
        ]);
    }

    // Update info pribadi
    public function updateInfo(Request $request)
    {
        $this->cekLogin();
        $user_id = session('user_id');

        $nama    = trim($request->input('nama', ''));
        $email   = trim($request->input('email', ''));
        $telepon = trim($request->input('telepon', ''));
        $lokasi  = trim($request->input('lokasi', ''));
        $bio     = trim($request->input('bio', ''));

        if (!$nama || !$email) {
            return response()->json(['status' => 'error', 'message' => 'Nama dan email wajib diisi.']);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['status' => 'error', 'message' => 'Format email tidak valid.']);
        }

        $existing = DB::selectOne('SELECT id FROM users WHERE email = ? AND id != ?', [$email, $user_id]);
        if ($existing) {
            return response()->json(['status' => 'error', 'message' => 'Email sudah digunakan akun lain.']);
        }

        DB::update('
            UPDATE users SET nama = ?, email = ?, telepon = ?, lokasi = ?, bio = ?, updated_at = NOW()
            WHERE id = ?
        ', [$nama, $email, $telepon, $lokasi, $bio, $user_id]);

        session(['nama' => $nama]);

        return response()->json(['status' => 'success', 'message' => 'Profil berhasil diperbarui.']);
    }

    // Update password
    public function updatePassword(Request $request)
    {
        $this->cekLogin();
        $user_id = session('user_id');

        $old_pass     = $request->input('old_password', '');
        $new_pass     = $request->input('new_password', '');
        $confirm_pass = $request->input('confirm_password', '');

        if (!$old_pass || !$new_pass || !$confirm_pass) {
            return response()->json(['status' => 'error', 'message' => 'Semua kolom password wajib diisi.']);
        }
        if (strlen($new_pass) < 8) {
            return response()->json(['status' => 'error', 'message' => 'Password baru minimal 8 karakter.']);
        }
        if ($new_pass !== $confirm_pass) {
            return response()->json(['status' => 'error', 'message' => 'Konfirmasi password tidak cocok.']);
        }

        $user = DB::selectOne('SELECT password FROM users WHERE id = ?', [$user_id]);

        if (!Hash::check($old_pass, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Password saat ini salah.']);
        }

        DB::update('UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?', [
            Hash::make($new_pass), $user_id
        ]);

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diubah.']);
    }

    // Hapus akun
    public function hapusAkun()
    {
        $this->cekLogin();
        $user_id = session('user_id');

        DB::delete('DELETE FROM users WHERE id = ?', [$user_id]);
        session()->flush();

        return response()->json(['status' => 'success', 'message' => 'Akun berhasil dihapus.']);
    }
}