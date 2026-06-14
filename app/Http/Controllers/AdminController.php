<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ─── Guard ──────────────────────────────────────────────────────
    private function guard()
    {
        if (!session('user_id') || session('role') !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
    }

    // ─── Dashboard ──────────────────────────────────────────────────
    public function dashboard()
    {
        $this->guard();

        $totalUsers      = DB::selectOne("SELECT COUNT(*) AS c FROM users WHERE role = 'user'")->c;
        $totalRecruiters = DB::selectOne("SELECT COUNT(*) AS c FROM users WHERE role = 'recruiter'")->c;
        $totalJobs       = DB::selectOne("SELECT COUNT(*) AS c FROM jobs")->c;
        $totalLamaran    = DB::selectOne("SELECT COUNT(*) AS c FROM lamaran")->c;
        $totalSkills     = DB::selectOne("SELECT COUNT(*) AS c FROM skills")->c;
        $totalPerusahaan = DB::selectOne("SELECT COUNT(*) AS c FROM perusahaan")->c;

        $pendingRecruiters = DB::selectOne("SELECT COUNT(*) AS c FROM users WHERE role = 'recruiter' AND is_verified = 0")->c;

        // Lamaran per status
        $lamaranStats = DB::select("SELECT status, COUNT(*) AS c FROM lamaran GROUP BY status");
        $lamaranMap = [];
        foreach ($lamaranStats as $row) {
            $lamaranMap[$row->status] = $row->c;
        }

        // Recent users (last 5)
        $recentUsers = DB::select("SELECT id, nama, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");

        // Recent jobs (last 5)
        $recentJobs = DB::select("SELECT id, nama_posisi, nama_perusahaan, created_at FROM jobs ORDER BY created_at DESC LIMIT 5");

        return view('admin.dashboard', compact(
            'totalUsers', 'totalRecruiters', 'totalJobs', 'totalLamaran',
            'totalSkills', 'totalPerusahaan', 'pendingRecruiters',
            'lamaranMap', 'recentUsers', 'recentJobs'
        ));
    }

    // ─── Jobs ───────────────────────────────────────────────────────
    public function jobs(Request $request)
    {
        $this->guard();

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

        $tipeMap = [
            'full-time'   => ['class' => '',        'label' => 'Full Time'],
            'part-time'   => ['class' => 'parttime', 'label' => 'Part Time'],
            'remote'      => ['class' => 'remote',   'label' => 'Remote'],
            'hybrid'      => ['class' => 'hybrid',   'label' => 'Hybrid'],
            'contract'    => ['class' => 'contract', 'label' => 'Contract'],
            'partnership' => ['class' => 'partner',  'label' => 'Partnership'],
        ];

        foreach ($jobs as $job) {
            $map = $tipeMap[$job->tipe] ?? ['class' => '', 'label' => ucfirst($job->tipe)];
            $job->tipe_class = $map['class'];
            $job->tipe_label = $map['label'];
        }

        return view('admin.jobs', compact('jobs', 'sort', 'dir'));
    }

    public function deleteJob($id)
    {
        $this->guard();
        DB::delete("DELETE FROM jobs WHERE id = ?", [$id]);
        return redirect('/admin/jobs')->with('success', 'Lowongan berhasil dihapus.');
    }

    // ─── Users ──────────────────────────────────────────────────────
    public function users(Request $request)
    {
        $this->guard();

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
            SELECT id, nama, username, email, telepon, role, is_verified, created_at
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
        $this->guard();

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
        $this->guard();
        DB::update("UPDATE users SET is_verified = 1 WHERE id = ? AND role = 'recruiter'", [$id]);
        return redirect()->back()->with('success', 'Recruiter berhasil diverifikasi.');
    }

    public function deleteUser($id)
    {
        $this->guard();
        if ($id == session('user_id')) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        DB::delete("DELETE FROM users WHERE id = ?", [$id]);
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    // ─── Skills ─────────────────────────────────────────────────────
    public function skills()
    {
        $this->guard();

        $skills  = DB::select("SELECT * FROM skills ORDER BY kategori, nama");
        $grouped = [];
        foreach ($skills as $s) {
            $grouped[$s->kategori][] = $s;
        }

        $totalSkills = count($skills);
        return view('admin.skills', compact('grouped', 'totalSkills'));
    }

    public function addSkill(Request $request)
    {
        $this->guard();

        $request->validate([
            'nama'     => 'required|string|max:100',
            'kategori' => 'required|in:teknologi,desain,marketing,keuangan,manajemen,kesehatan,pendidikan,teknik,hukum,lainnya',
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
        $this->guard();
        DB::delete("DELETE FROM skills WHERE id = ?", [$id]);
        return redirect('/admin/skills')->with('success', 'Skill berhasil dihapus.');
    }
}
