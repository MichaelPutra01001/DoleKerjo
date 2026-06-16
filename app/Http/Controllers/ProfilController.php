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
                   pendidikan, jurusan, tanggal_lahir, gender, foto_profil,
                   cv, portfolio, email_verified, created_at
            FROM users WHERE id = ?
        ', [$user_id]);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan.'], 404);
        }

        // ── Profile completion ──
        $skills = DB::select('
            SELECT us.skill_id, s.nama, s.kategori, us.level
            FROM user_skills us
            JOIN skills s ON s.id = us.skill_id
            WHERE us.user_id = ?
        ', [$user_id]);

        $steps = $this->calcCompletion($user, $skills);

        $lamaran = DB::select('
            SELECT l.status, l.created_at, j.nama_posisi, j.nama_perusahaan
            FROM lamaran l
            JOIN jobs j ON j.id = l.job_id
            WHERE l.user_id = ?
            ORDER BY l.created_at DESC
        ', [$user_id]);

        return response()->json([
            'status'    => 'success',
            'user'      => $user,
            'skills'    => $skills,
            'lamaran'   => $lamaran,
            'steps'     => $steps,
            'completion'=> $steps['percent'],
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

    // ── Upload CV (DOCX/PDF) ──
    public function uploadCV(Request $request)
    {
        $this->cekLogin();
        $user_id = session('user_id');

        if (!$request->hasFile('cv')) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada file yang diunggah.']);
        }

        $file = $request->file('cv');
        $ext  = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['pdf', 'docx', 'doc'])) {
            return response()->json(['status' => 'error', 'message' => 'Format file harus PDF, DOCX, atau DOC.']);
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return response()->json(['status' => 'error', 'message' => 'Ukuran file maksimal 5 MB.']);
        }

        $filename = 'cv_' . $user_id . '_' . time() . '.' . $ext;
        $dest     = public_path('uploads/cv');
        if (!is_dir($dest)) mkdir($dest, 0755, true);
        $file->move($dest, $filename);

        // Delete old file
        $old = DB::selectOne('SELECT cv FROM users WHERE id = ?', [$user_id]);
        if ($old && $old->cv && file_exists(public_path($old->cv))) {
            @unlink(public_path($old->cv));
        }

        $path = 'uploads/cv/' . $filename;
        DB::update('UPDATE users SET cv = ?, updated_at = NOW() WHERE id = ?', [$path, $user_id]);

        return response()->json(['status' => 'success', 'message' => 'CV berhasil diunggah!', 'path' => $path]);
    }

    // ── Delete CV ──
    public function deleteCV()
    {
        $this->cekLogin();
        $user_id = session('user_id');

        $old = DB::selectOne('SELECT cv FROM users WHERE id = ?', [$user_id]);
        if ($old && $old->cv && file_exists(public_path($old->cv))) {
            @unlink(public_path($old->cv));
        }

        DB::update('UPDATE users SET cv = NULL, updated_at = NOW() WHERE id = ?', [$user_id]);

        return response()->json(['status' => 'success', 'message' => 'CV berhasil dihapus.']);
    }

    // ── Upload foto profil ──
    public function uploadPhoto(Request $request)
    {
        $this->cekLogin();
        $user_id = session('user_id');

        if (!$request->hasFile('foto')) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada file yang diunggah.']);
        }

        $file = $request->file('foto');
        $ext  = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return response()->json(['status' => 'error', 'message' => 'Format file harus JPG, PNG, atau WEBP.']);
        }
        if ($file->getSize() > 3 * 1024 * 1024) {
            return response()->json(['status' => 'error', 'message' => 'Ukuran file maksimal 3 MB.']);
        }

        $filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
        $dest     = public_path('uploads/avatar');
        if (!is_dir($dest)) mkdir($dest, 0755, true);
        $file->move($dest, $filename);

        // Delete old file
        $old = DB::selectOne('SELECT foto_profil FROM users WHERE id = ?', [$user_id]);
        if ($old && $old->foto_profil && file_exists(public_path($old->foto_profil))) {
            @unlink(public_path($old->foto_profil));
        }

        $path = 'uploads/avatar/' . $filename;
        DB::update('UPDATE users SET foto_profil = ?, updated_at = NOW() WHERE id = ?', [$path, $user_id]);

        return response()->json(['status' => 'success', 'message' => 'Foto profil berhasil diunggah!', 'path' => $path]);
    }

    // ── Upload portfolio ──
    public function uploadPortfolio(Request $request)
    {
        $this->cekLogin();
        $user_id = session('user_id');

        if (!$request->hasFile('portfolio')) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada file yang diunggah.']);
        }

        $file = $request->file('portfolio');
        $ext  = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['pdf', 'docx', 'doc', 'pptx', 'zip'])) {
            return response()->json(['status' => 'error', 'message' => 'Format file harus PDF, DOCX, DOC, PPTX, atau ZIP.']);
        }
        if ($file->getSize() > 10 * 1024 * 1024) {
            return response()->json(['status' => 'error', 'message' => 'Ukuran file maksimal 10 MB.']);
        }

        $filename = 'portfolio_' . $user_id . '_' . time() . '.' . $ext;
        $dest     = public_path('uploads/portfolio');
        if (!is_dir($dest)) mkdir($dest, 0755, true);
        $file->move($dest, $filename);

        // Delete old file
        $old = DB::selectOne('SELECT portfolio FROM users WHERE id = ?', [$user_id]);
        if ($old && $old->portfolio && file_exists(public_path($old->portfolio))) {
            @unlink(public_path($old->portfolio));
        }

        $path = 'uploads/portfolio/' . $filename;
        DB::update('UPDATE users SET portfolio = ?, updated_at = NOW() WHERE id = ?', [$path, $user_id]);

        return response()->json(['status' => 'success', 'message' => 'Portfolio berhasil diunggah!', 'path' => $path]);
    }

    // ── Get available skills list (for dropdown) ──
    public function skillsList()
    {
        $this->cekLogin();
        $user_id = session('user_id');

        $skills = DB::select('
            SELECT s.id, s.nama, s.kategori
            FROM skills s
            WHERE s.id NOT IN (SELECT skill_id FROM user_skills WHERE user_id = ?)
            ORDER BY s.kategori, s.nama
        ', [$user_id]);

        return response()->json(['status' => 'success', 'skills' => $skills]);
    }

    // ── Add skill to user ──
    public function addSkill(Request $request)
    {
        $this->cekLogin();
        $user_id = session('user_id');

        $skill_id = $request->input('skill_id');
        $level    = $request->input('level', 'pemula');

        if (!$skill_id) {
            return response()->json(['status' => 'error', 'message' => 'Pilih skill terlebih dahulu.']);
        }

        // Check if already added
        $exists = DB::selectOne('SELECT id FROM user_skills WHERE user_id = ? AND skill_id = ?', [$user_id, $skill_id]);
        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'Skill sudah ditambahkan.']);
        }

        if (!in_array($level, ['pemula', 'menengah', 'mahir'])) {
            $level = 'pemula';
        }

        DB::insert('INSERT INTO user_skills (user_id, skill_id, level) VALUES (?, ?, ?)', [$user_id, $skill_id, $level]);

        return response()->json(['status' => 'success', 'message' => 'Skill berhasil ditambahkan.']);
    }

    // ── Remove skill from user ──
    public function removeSkill($skillId)
    {
        $this->cekLogin();
        $user_id = session('user_id');

        DB::delete('DELETE FROM user_skills WHERE user_id = ? AND skill_id = ?', [$user_id, $skillId]);

        return response()->json(['status' => 'success', 'message' => 'Skill berhasil dihapus.']);
    }

    // ── Request email verification (sent to admin for approval) ──
    public function verifyEmail()
    {
        $this->cekLogin();
        $user_id = session('user_id');

        // Check current state
        $user = DB::selectOne('SELECT email_verified FROM users WHERE id = ?', [$user_id]);
        if ($user && $user->email_verified == 1) {
            return response()->json(['status' => 'error', 'message' => 'Email sudah diverifikasi.']);
        }
        if ($user && $user->email_verified == 2) {
            return response()->json(['status' => 'error', 'message' => 'Permintaan verifikasi sudah dikirim. Menunggu persetujuan admin.']);
        }

        // Set to pending (2 = waiting admin approval)
        DB::update('UPDATE users SET email_verified = 2, updated_at = NOW() WHERE id = ?', [$user_id]);

        return response()->json(['status' => 'success', 'message' => 'Permintaan verifikasi email berhasil dikirim. Menunggu persetujuan admin.']);
    }

    // ── Calculate profile completion steps ──
    private function calcCompletion($user, $skills)
    {
        $steps = [
            ['id' => 'foto',     'label' => 'Upload foto profil',          'done' => !empty($user->foto_profil)],
            ['id' => 'info',     'label' => 'Lengkapi data pribadi',       'done' => !empty($user->lokasi) && !empty($user->pendidikan)],
            ['id' => 'bio',      'label' => 'Tulis tentang saya',          'done' => !empty($user->bio)],
            ['id' => 'cv',       'label' => 'Upload CV',                   'done' => !empty($user->cv)],
            ['id' => 'skill',    'label' => 'Tambah keahlian',             'done' => count($skills) > 0],
            ['id' => 'email',    'label' => 'Verifikasi email',            'done' => !empty($user->email_verified) && $user->email_verified == 1],
        ];

        $doneCount = collect($steps)->where('done', true)->count();
        $total     = count($steps);
        $percent   = round(($doneCount / $total) * 100);

        return [
            'items'   => $steps,
            'done'    => $doneCount,
            'total'   => $total,
            'percent' => $percent,
        ];
    }
}