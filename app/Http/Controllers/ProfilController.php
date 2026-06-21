<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    // autentikasi diurus sama middleware auth.check

    // tampilin halaman profil
    public function index()
    {
        return view('profil');
    }

    // ambil data profil dalam format JSON
    public function getData()
    {
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

        // hitung kelengkapan profil
        $skills = DB::select('
            SELECT us.skill_id, s.nama, s.kategori, us.level
            FROM user_skills us
            JOIN skills s ON s.id = us.skill_id
            WHERE us.user_id = ?
        ', [$user_id]);

        $steps = $this->calcCompletion($user, $skills);

        // bersihin lamaran yang jobnya udah dihapus (biar ga error)
        DB::delete('
            DELETE l FROM lamaran l
            LEFT JOIN jobs j ON j.id = l.job_id
            WHERE l.user_id = ? AND j.id IS NULL
        ', [$user_id]);

        $lamaran = DB::select('
            SELECT l.status, l.catatan, l.created_at, l.updated_at, j.nama_posisi, j.nama_perusahaan, j.id AS job_id
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

    // update info pribadi user
    public function updateInfo(Request $request)
    {
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

    // ganti password user
    public function updatePassword(Request $request)
    {
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

    // hapus akun beserta semua data yang nyambung ke akun ini
    public function hapusAkun()
    {
        $user_id = session('user_id');

        DB::transaction(function () use ($user_id) {
            // kalau recruiter, hapus juga data perusahaan dan job-jobnya
            $user = DB::selectOne('SELECT role FROM users WHERE id = ?', [$user_id]);
            if ($user && $user->role === 'recruiter') {
                $perusahaan = DB::selectOne('SELECT id, nama FROM perusahaan WHERE recruiter_id = ?', [$user_id]);
                if ($perusahaan) {
                    DB::delete('DELETE l FROM lamaran l JOIN jobs j ON l.job_id = j.id WHERE j.recruiter_id = ?', [$user_id]);
                    DB::delete('DELETE FROM jobs WHERE recruiter_id = ?', [$user_id]);
                    DB::delete('DELETE FROM reviews WHERE nama_perusahaan = ?', [$perusahaan->nama]);
                    DB::delete('DELETE FROM perusahaan WHERE recruiter_id = ?', [$user_id]);
                }
            } else {
                DB::delete('DELETE FROM lamaran WHERE user_id = ?', [$user_id]);
                DB::delete('DELETE FROM user_skills WHERE user_id = ?', [$user_id]);
                DB::delete('DELETE FROM reviews WHERE user_id = ?', [$user_id]);
            }

            // hapus file yang pernah diupload
            $userData = DB::selectOne('SELECT cv, foto_profil, portfolio FROM users WHERE id = ?', [$user_id]);
            if ($userData) {
                foreach ([$userData->cv, $userData->foto_profil, $userData->portfolio] as $file) {
                    if ($file && file_exists(public_path($file))) {
                        @unlink(public_path($file));
                    }
                }
            }

            DB::delete('DELETE FROM users WHERE id = ?', [$user_id]);
        });

        session()->flush();
        return response()->json(['status' => 'success', 'message' => 'Akun berhasil dihapus.']);
    }

    // upload CV (PDF/DOCX)
    public function uploadCV(Request $request)
    {
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

        // hapus CV lama kalau ada
        $old = DB::selectOne('SELECT cv FROM users WHERE id = ?', [$user_id]);
        if ($old && $old->cv && file_exists(public_path($old->cv))) {
            @unlink(public_path($old->cv));
        }

        $path = 'uploads/cv/' . $filename;

        // coba parse CV ke markdown pakai markitdown
        $cvParsed = null;
        try {
            $fullPath = $dest . '/' . $filename;
            $cmd = 'python -m markitdown ' . escapeshellarg($fullPath) . ' 2>&1';
            $output = shell_exec($cmd);
            if ($output && strlen(trim($output)) > 10) {
                $cvParsed = trim($output);
            }
        } catch (\Throwable $e) {
            // ga masalah kalau parse gagal, CV tetap tersimpan
            $cvParsed = null;
        }

        DB::update('UPDATE users SET cv = ?, cv_parsed = ?, updated_at = NOW() WHERE id = ?', [$path, $cvParsed, $user_id]);

        return response()->json([
            'status'  => 'success',
            'message' => 'CV berhasil diunggah!' . ($cvParsed ? ' (AI berhasil membaca CV)' : ''),
            'path'    => $path,
            'parsed'  => $cvParsed !== null,
        ]);
    }

    // hapus CV
    public function deleteCV()
    {
        $user_id = session('user_id');

        $old = DB::selectOne('SELECT cv FROM users WHERE id = ?', [$user_id]);
        if ($old && $old->cv && file_exists(public_path($old->cv))) {
            @unlink(public_path($old->cv));
        }

        DB::update('UPDATE users SET cv = NULL, cv_parsed = NULL, updated_at = NOW() WHERE id = ?', [$user_id]);

        return response()->json(['status' => 'success', 'message' => 'CV berhasil dihapus.']);
    }

    // upload foto profil
    public function uploadPhoto(Request $request)
    {
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

        // hapus foto lama kalau ada
        $old = DB::selectOne('SELECT foto_profil FROM users WHERE id = ?', [$user_id]);
        if ($old && $old->foto_profil && file_exists(public_path($old->foto_profil))) {
            @unlink(public_path($old->foto_profil));
        }

        $path = 'uploads/avatar/' . $filename;
        DB::update('UPDATE users SET foto_profil = ?, updated_at = NOW() WHERE id = ?', [$path, $user_id]);

        return response()->json(['status' => 'success', 'message' => 'Foto profil berhasil diunggah!', 'path' => $path]);
    }

    // upload portfolio
    public function uploadPortfolio(Request $request)
    {
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

        // hapus portfolio lama kalau ada
        $old = DB::selectOne('SELECT portfolio FROM users WHERE id = ?', [$user_id]);
        if ($old && $old->portfolio && file_exists(public_path($old->portfolio))) {
            @unlink(public_path($old->portfolio));
        }

        $path = 'uploads/portfolio/' . $filename;
        DB::update('UPDATE users SET portfolio = ?, updated_at = NOW() WHERE id = ?', [$path, $user_id]);

        return response()->json(['status' => 'success', 'message' => 'Portfolio berhasil diunggah!', 'path' => $path]);
    }

    // ambil daftar skill yang belum dimiliki user (buat dropdown)
    public function skillsList()
    {
        $user_id = session('user_id');

        $skills = DB::select('
            SELECT s.id, s.nama, s.kategori
            FROM skills s
            WHERE s.id NOT IN (SELECT skill_id FROM user_skills WHERE user_id = ?)
            ORDER BY s.kategori, s.nama
        ', [$user_id]);

        return response()->json(['status' => 'success', 'skills' => $skills]);
    }

    // tambah skill ke profil user
    public function addSkill(Request $request)
    {
        $user_id = session('user_id');

        $skill_id = $request->input('skill_id');
        $level    = $request->input('level', 'pemula');

        if (!$skill_id) {
            return response()->json(['status' => 'error', 'message' => 'Pilih skill terlebih dahulu.']);
        }

        // cek apakah skill ini udah ditambah sebelumnya
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

    // hapus skill dari profil user
    public function removeSkill($skillId)
    {
        $user_id = session('user_id');

        DB::delete('DELETE FROM user_skills WHERE user_id = ? AND skill_id = ?', [$user_id, $skillId]);

        return response()->json(['status' => 'success', 'message' => 'Skill berhasil dihapus.']);
    }

    // minta verifikasi email (dikirim ke admin buat disetujui)
    public function verifyEmail()
    {
        $user_id = session('user_id');

        // cek status verifikasi sekarang
        $user = DB::selectOne('SELECT email_verified FROM users WHERE id = ?', [$user_id]);
        if ($user && $user->email_verified == 1) {
            return response()->json(['status' => 'error', 'message' => 'Email sudah diverifikasi.']);
        }
        if ($user && $user->email_verified == 2) {
            return response()->json(['status' => 'error', 'message' => 'Permintaan verifikasi sudah dikirim. Menunggu persetujuan admin.']);
        }

        // set ke pending (2 = nunggu admin approve)
        DB::update('UPDATE users SET email_verified = 2, updated_at = NOW() WHERE id = ?', [$user_id]);

        return response()->json(['status' => 'success', 'message' => 'Permintaan verifikasi email berhasil dikirim. Menunggu persetujuan admin.']);
    }

    // hitung persentase kelengkapan profil
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
