<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // autentikasi diurus sama middleware admin.only

    // === bagian dashboard ===
    public function dashboard()
    {

        $totalUsers      = DB::selectOne("SELECT COUNT(*) AS c FROM users WHERE role = 'user'")->c;
        $totalRecruiters = DB::selectOne("SELECT COUNT(*) AS c FROM users WHERE role = 'recruiter'")->c;
        $totalJobs       = DB::selectOne("SELECT COUNT(*) AS c FROM jobs")->c;
        $totalLamaran    = DB::selectOne("SELECT COUNT(*) AS c FROM lamaran")->c;
        $totalSkills     = DB::selectOne("SELECT COUNT(*) AS c FROM skills")->c;
        $totalPerusahaan = DB::selectOne("SELECT COUNT(*) AS c FROM perusahaan")->c;

        $pendingRecruiters = DB::selectOne("SELECT COUNT(*) AS c FROM users WHERE role = 'recruiter' AND is_verified = 0")->c;

        // ngitung lamaran per status
        $lamaranStats = DB::select("SELECT status, COUNT(*) AS c FROM lamaran GROUP BY status");
        $lamaranMap = [];
        foreach ($lamaranStats as $row) {
            $lamaranMap[$row->status] = $row->c;
        }

        // ambil 5 user terbaru
        $recentUsers = DB::select("SELECT id, nama, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");

        // ambil 5 job terbaru
        $recentJobs = DB::select("SELECT id, nama_posisi, nama_perusahaan, created_at FROM jobs ORDER BY created_at DESC LIMIT 5");

        return view('admin.dashboard', compact(
            'totalUsers', 'totalRecruiters', 'totalJobs', 'totalLamaran',
            'totalSkills', 'totalPerusahaan', 'pendingRecruiters',
            'lamaranMap', 'recentUsers', 'recentJobs'
        ));
    }

    // === bagian jobs ===
    public function jobs(Request $request)
    {

        $sort  = $request->get('sort', 'id');
        $dir   = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowed = ['id', 'created_at', 'nama_posisi', 'nama_perusahaan', 'tipe'];
        if (!in_array($sort, $allowed)) $sort = 'id';
        $dirSQL = strtoupper($dir);

        $jobs = DB::select("
            SELECT j.*, u.nama AS recruiter_nama
            FROM jobs j
            LEFT JOIN users u ON j.recruiter_id = u.id
            ORDER BY j.{$sort} {$dirSQL}
        ");

        $tipeMap = config('tipe_map');

        foreach ($jobs as $job) {
            $map = $tipeMap[$job->tipe] ?? ['class' => '', 'label' => ucfirst($job->tipe)];
            $job->tipe_class = $map['class'];
            $job->tipe_label = $map['label'];
        }

        return view('admin.jobs', compact('jobs', 'sort', 'dir'));
    }

    public function deleteJob($id)
    {
        // hapus dulu lamaran yang nyambung ke job ini biar ga error
        DB::delete('DELETE FROM lamaran WHERE job_id = ?', [$id]);
        DB::delete('DELETE FROM jobs WHERE id = ?', [$id]);
        return redirect('/admin/jobs')->with('success', 'Lowongan berhasil dihapus.');
    }

    // === bagian users ===
    public function users(Request $request)
    {

        $search = trim($request->get('search', ''));
        $role   = $request->get('role', '');
        $sort   = $request->get('sort', 'id');
        $dir    = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $page   = max(1, intval($request->get('page', 1)));
        $perPage = 10;

        $allowed = ['id', 'created_at', 'nama', 'email'];
        if (!in_array($sort, $allowed)) $sort = 'id';
        $dirSQL = strtoupper($dir);

        $params = [];
        $where  = [];

        if ($search !== '') {
            $where[] = '(nama LIKE ? OR username LIKE ? OR email LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($role !== '' && in_array($role, ['admin', 'recruiter', 'user'])) {
            $where[] = 'role = ?';
            $params[] = $role;
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $total = DB::selectOne("SELECT COUNT(*) AS c FROM users {$whereClause}", $params)->c;
        $totalPages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $users = DB::select("
            SELECT id, nama, username, email, telepon, role, is_verified, email_verified, created_at
            FROM users {$whereClause}
            ORDER BY {$sort} {$dirSQL}
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        return view('admin.users', compact(
            'users', 'sort', 'dir', 'search', 'role',
            'page', 'totalPages', 'total'
        ));
    }

    public function recruiterDetail($id)
    {

        $user = DB::selectOne("
            SELECT id, nama, username, email, telepon, role, is_verified, created_at
            FROM users WHERE id = ? AND role = 'recruiter'
        ", [$id]);

        if (!$user) {
            return redirect('/admin/users')->with('error', 'Recruiter tidak ditemukan.');
        }

        $perusahaan = DB::selectOne("
            SELECT nama, lokasi, website, tipe_bisnis, ditemukan_tahun, deskripsi, logo, created_at
            FROM perusahaan WHERE recruiter_id = ?
        ", [$id]);

        $totalJobs = DB::selectOne("SELECT COUNT(*) AS c FROM jobs WHERE recruiter_id = ?", [$id])->c;

        return view('admin.recruiter_detail', compact('user', 'perusahaan', 'totalJobs'));
    }

    public function verifyRecruiter($id)
    {
        DB::update("UPDATE users SET is_verified = 1 WHERE id = ? AND role = 'recruiter'", [$id]);
        return redirect()->back()->with('success', 'Recruiter berhasil diverifikasi.');
    }

    public function verifyUserEmail($id)
    {
        DB::update("UPDATE users SET email_verified = 1 WHERE id = ?", [$id]);
        return redirect()->back()->with('success', 'Email user berhasil diverifikasi.');
    }

    public function deleteUser($id)
    {
        if ($id == session('user_id')) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // hapus semua data yang nyambung ke user ini
        DB::transaction(function () use ($id) {
            $user = DB::selectOne('SELECT role FROM users WHERE id = ?', [$id]);
            if ($user && $user->role === 'recruiter') {
                $perusahaan = DB::selectOne('SELECT id, nama FROM perusahaan WHERE recruiter_id = ?', [$id]);
                if ($perusahaan) {
                    DB::delete('DELETE l FROM lamaran l JOIN jobs j ON l.job_id = j.id WHERE j.recruiter_id = ?', [$id]);
                    DB::delete('DELETE FROM jobs WHERE recruiter_id = ?', [$id]);
                    DB::delete('DELETE FROM reviews WHERE nama_perusahaan = ?', [$perusahaan->nama]);
                    DB::delete('DELETE FROM perusahaan WHERE recruiter_id = ?', [$id]);
                }
            } else {
                DB::delete('DELETE FROM lamaran WHERE user_id = ?', [$id]);
                DB::delete('DELETE FROM user_skills WHERE user_id = ?', [$id]);
                DB::delete('DELETE FROM reviews WHERE user_id = ?', [$id]);
            }

            // hapus file yang udah diupload
            $userData = DB::selectOne('SELECT cv, foto_profil, portfolio FROM users WHERE id = ?', [$id]);
            if ($userData) {
                foreach ([$userData->cv, $userData->foto_profil, $userData->portfolio] as $file) {
                    if ($file && file_exists(public_path($file))) {
                        @unlink(public_path($file));
                    }
                }
            }

            DB::delete('DELETE FROM users WHERE id = ?', [$id]);
        });

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    // === bagian skills ===
    public function skills()
    {

        // ambil kategori dari db secara dinamis
        $kategoriList = DB::select("
            SELECT k.*, (SELECT COUNT(*) FROM jobs WHERE kategori = k.nama) AS job_count
            FROM kategori k
            ORDER BY job_count DESC, k.nama ASC
        ");

        $skills  = DB::select("SELECT * FROM skills ORDER BY kategori, nama");
        $grouped = [];
        foreach ($skills as $s) {
            $grouped[$s->kategori][] = $s;
        }

        $totalSkills = count($skills);
        return view('admin.skills', compact('grouped', 'totalSkills', 'kategoriList'));
    }

    public function addSkill(Request $request)
    {

        $request->validate([
            'nama'     => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
        ]);

        try {
            DB::insert("INSERT INTO skills (nama, kategori) VALUES (?, ?)", [
                $request->nama, $request->kategori
            ]);
            return redirect('/admin/skills')->with('success', 'Skill berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect('/admin/skills')->with('error', 'Skill sudah ada atau gagal ditambahkan.');
        }
    }

    public function deleteSkill($id)
    {
        DB::delete("DELETE FROM skills WHERE id = ?", [$id]);
        return redirect('/admin/skills')->with('success', 'Skill berhasil dihapus.');
    }

    // === bagian kategori CRUD ===
    public function addKategori(Request $request)
    {

        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        try {
            DB::insert("INSERT INTO kategori (nama) VALUES (?)", [$request->nama]);
            return redirect('/admin/skills')->with('success', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect('/admin/skills')->with('error', 'Kategori sudah ada atau gagal ditambahkan.');
        }
    }

    public function deleteKategori($id)
    {

        // cek dulu apakah ada job yang pakai kategori ini
        $kat = DB::selectOne("SELECT nama FROM kategori WHERE id = ?", [$id]);
        if ($kat) {
            $jobCount = DB::selectOne("SELECT COUNT(*) AS c FROM jobs WHERE kategori = ?", [$kat->nama])->c;
            if ($jobCount > 0) {
                return redirect('/admin/skills')->with('error', "Tidak dapat menghapus kategori \"{$kat->nama}\" karena masih digunakan oleh {$jobCount} lowongan.");
            }
        }

        DB::delete("DELETE FROM kategori WHERE id = ?", [$id]);
        return redirect('/admin/skills')->with('success', 'Kategori berhasil dihapus.');
    }
}
