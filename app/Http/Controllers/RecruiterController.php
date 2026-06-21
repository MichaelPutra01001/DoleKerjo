<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecruiterController extends Controller
{
    // autentikasi diurus sama middleware recruiter.only

    private function userId()
    {
        return session('user_id');
    }

    // === bagian dashboard ===
    public function dashboard()
    {
        $uid = $this->userId();

        $totalJobs = DB::selectOne("SELECT COUNT(*) AS c FROM jobs WHERE recruiter_id = ?", [$uid])->c;

        $totalLamaran = DB::selectOne("
            SELECT COUNT(*) AS c FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            WHERE j.recruiter_id = ?
        ", [$uid])->c;

        $pendingLamaran = DB::selectOne("
            SELECT COUNT(*) AS c FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            WHERE j.recruiter_id = ? AND l.status = 'pending'
        ", [$uid])->c;

        $interviewCount = DB::selectOne("
            SELECT COUNT(*) AS c FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            WHERE j.recruiter_id = ? AND l.status = 'interview'
        ", [$uid])->c;

        $diterimaCount = DB::selectOne("
            SELECT COUNT(*) AS c FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            WHERE j.recruiter_id = ? AND l.status = 'diterima'
        ", [$uid])->c;

        $ditolakCount = DB::selectOne("
            SELECT COUNT(*) AS c FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            WHERE j.recruiter_id = ? AND l.status = 'ditolak'
        ", [$uid])->c;

        // ambil 8 pelamar terbaru
        $recentApplicants = DB::select("
            SELECT l.id, l.status, l.created_at, u.nama, u.email, j.nama_posisi, j.id AS job_id
            FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            JOIN users u ON l.user_id = u.id
            WHERE j.recruiter_id = ?
            ORDER BY l.created_at DESC LIMIT 8
        ", [$uid]);

        // ambil 5 job terbaru
        $recentJobs = DB::select("
            SELECT id, nama_posisi, nama_perusahaan, tipe, created_at
            FROM jobs WHERE recruiter_id = ?
            ORDER BY created_at DESC LIMIT 5
        ", [$uid]);

        // ambil rating perusahaan dari tabel reviews
        $companyRating = DB::selectOne("
            SELECT
                COUNT(*) AS total,
                ROUND(AVG(rating), 1) AS avg_rating
            FROM reviews r
            JOIN perusahaan p ON p.nama = r.nama_perusahaan
            WHERE p.recruiter_id = ?
        ", [$uid]);

        return view('recruiter.dashboard', compact(
            'totalJobs', 'totalLamaran', 'pendingLamaran',
            'interviewCount', 'diterimaCount', 'ditolakCount',
            'recentApplicants', 'recentJobs', 'companyRating'
        ));
    }

    // === bagian jobs ===
    public function jobs(Request $request)
    {
        $uid = $this->userId();

        $sort  = $request->get('sort', 'id');
        $dir   = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowed = ['id', 'created_at', 'nama_posisi', 'nama_perusahaan', 'tipe'];
        if (!in_array($sort, $allowed)) $sort = 'id';
        $dirSQL = strtoupper($dir);

        $jobs = DB::select("
            SELECT j.*,
                (SELECT COUNT(*) FROM lamaran WHERE job_id = j.id) AS pelamar_count
            FROM jobs j
            WHERE j.recruiter_id = ?
            ORDER BY j.{$sort} {$dirSQL}
        ", [$uid]);

        $tipeMap = config('tipe_map');

        foreach ($jobs as $job) {
            $map = $tipeMap[$job->tipe] ?? ['class' => '', 'label' => ucfirst($job->tipe)];
            $job->tipe_class = $map['class'];
            $job->tipe_label = $map['label'];
        }

        $kategoriList = DB::select("SELECT nama FROM kategori ORDER BY nama ASC");

        return view('recruiter.jobs', compact('jobs', 'sort', 'dir', 'kategoriList'));
    }

    public function storeJob(Request $request)
    {
        $uid = $this->userId();

        $request->validate([
            'nama_posisi'     => 'required|string|max:100',
            'nama_perusahaan' => 'required|string|max:100',
            'lokasi'          => 'nullable|string|max:100',
            'tipe'            => 'required|in:full-time,part-time,remote,hybrid,contract,partnership',
            'kategori'        => 'required|string|max:100',
            'deskripsi'       => 'nullable|string',
            'requirement'     => 'nullable|string',
            'gaji_min'        => 'nullable|integer|min:0',
            'gaji_max'        => 'nullable|integer|min:0',
        ]);

        DB::insert("
            INSERT INTO jobs (nama_posisi, nama_perusahaan, lokasi, tipe, kategori, deskripsi, requirement, gaji_min, gaji_max, recruiter_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $request->nama_posisi,
            $request->nama_perusahaan,
            $request->lokasi,
            $request->tipe,
            $request->kategori,
            $request->deskripsi,
            $request->requirement,
            $request->gaji_min ?: null,
            $request->gaji_max ?: null,
            $uid,
        ]);

        return redirect()->back()->with('success', 'Lowongan berhasil ditambahkan.');
    }

    public function updateJob(Request $request, $id)
    {
        $uid = $this->userId();

        // pastiin job ini punyanya recruiter yang lagi login
        $job = DB::selectOne("SELECT id FROM jobs WHERE id = ? AND recruiter_id = ?", [$id, $uid]);
        if (!$job) abort(403, 'Lowongan tidak ditemukan.');

        $request->validate([
            'nama_posisi'     => 'required|string|max:100',
            'nama_perusahaan' => 'required|string|max:100',
            'lokasi'          => 'nullable|string|max:100',
            'tipe'            => 'required|in:full-time,part-time,remote,hybrid,contract,partnership',
            'kategori'        => 'required|string|max:100',
            'deskripsi'       => 'nullable|string',
            'requirement'     => 'nullable|string',
            'gaji_min'        => 'nullable|integer|min:0',
            'gaji_max'        => 'nullable|integer|min:0',
        ]);

        DB::update("
            UPDATE jobs SET nama_posisi=?, nama_perusahaan=?, lokasi=?, tipe=?, kategori=?, deskripsi=?, requirement=?, gaji_min=?, gaji_max=?
            WHERE id = ? AND recruiter_id = ?
        ", [
            $request->nama_posisi, $request->nama_perusahaan, $request->lokasi,
            $request->tipe, $request->kategori, $request->deskripsi, $request->requirement,
            $request->gaji_min ?: null, $request->gaji_max ?: null,
            $id, $uid,
        ]);

        return redirect()->back()->with('success', 'Lowongan berhasil diperbarui.');
    }

    public function deleteJob($id)
    {
        $uid = $this->userId();
        // hapus lamaran dulu sebelum hapus jobnya biar ga error foreign key
        DB::delete('DELETE FROM lamaran WHERE job_id = ? AND job_id IN (SELECT id FROM jobs WHERE recruiter_id = ?)', [$id, $uid]);
        DB::delete('DELETE FROM jobs WHERE id = ? AND recruiter_id = ?', [$id, $uid]);
        return redirect()->back()->with('success', 'Lowongan berhasil dihapus.');
    }

    public function getJobData($id)
    {
        $uid = $this->userId();
        $job = DB::selectOne("SELECT * FROM jobs WHERE id = ? AND recruiter_id = ?", [$id, $uid]);
        return response()->json($job);
    }

    // === bagian lamaran (pelamar) ===
    public function lamaran(Request $request)
    {
        $uid = $this->userId();

        $status = $request->get('status', '');
        $search = trim($request->get('search', ''));
        $page   = max(1, intval($request->get('page', 1)));
        $perPage = 10;

        $params = [$uid];
        $where  = ['j.recruiter_id = ?'];

        if ($status !== '' && in_array($status, ['pending', 'review', 'interview', 'diterima', 'ditolak'])) {
            $where[] = 'l.status = ?';
            $params[] = $status;
        }

        if ($search !== '') {
            $where[] = '(u.nama LIKE ? OR u.email LIKE ? OR j.nama_posisi LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $total = DB::selectOne("
            SELECT COUNT(*) AS c FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            JOIN users u ON l.user_id = u.id
            {$whereClause}
        ", $params)->c;

        $totalPages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $applicants = DB::select("
            SELECT l.id, l.status, l.catatan, l.created_at, l.updated_at,
                   u.nama, u.email, u.telepon, u.foto_profil, u.cv,
                   j.nama_posisi, j.id AS job_id
            FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            JOIN users u ON l.user_id = u.id
            {$whereClause}
            ORDER BY l.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        // hitung jumlah per status buat tombol filter
        $statusCounts = DB::select("
            SELECT l.status, COUNT(*) AS c FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            WHERE j.recruiter_id = ?
            GROUP BY l.status
        ", [$uid]);
        $countMap = [];
        foreach ($statusCounts as $row) { $countMap[$row->status] = $row->c; }

        return view('recruiter.lamaran', compact(
            'applicants', 'status', 'search', 'page', 'totalPages', 'total', 'countMap'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $uid = $this->userId();

        $request->validate(['status' => 'required|in:pending,review,interview,diterima,ditolak']);

        // pastiin lamaran ini memang milik job recruiter yang lagi login
        $exists = DB::selectOne("
            SELECT l.id FROM lamaran l
            JOIN jobs j ON l.job_id = j.id
            WHERE l.id = ? AND j.recruiter_id = ?
        ", [$id, $uid]);

        if (!$exists) abort(403, 'Lamaran tidak ditemukan.');

        DB::update("UPDATE lamaran SET status = ?, catatan = ? WHERE id = ?", [
            $request->status, $request->catatan, $id
        ]);

        return redirect()->back()->with('success', 'Status lamaran berhasil diperbarui.');
    }

    // === bagian profil perusahaan ===
    public function profil()
    {
        $uid = $this->userId();

        $perusahaan = DB::selectOne("SELECT * FROM perusahaan WHERE recruiter_id = ?", [$uid]);
        $user = DB::selectOne("SELECT nama, username, email, telepon FROM users WHERE id = ?", [$uid]);

        // ambil rating perusahaan
        $companyRating = null;
        if ($perusahaan) {
            $companyRating = DB::selectOne("
                SELECT COUNT(*) AS total, ROUND(AVG(rating), 1) AS avg_rating
                FROM reviews WHERE nama_perusahaan = ?
            ", [$perusahaan->nama]);
        }

        return view('recruiter.profil', compact('perusahaan', 'user', 'companyRating'));
    }

    public function updateProfil(Request $request)
    {
        $uid = $this->userId();

        $request->validate([
            'nama'            => 'required|string|max:100',
            'lokasi'          => 'nullable|string|max:100',
            'website'         => 'nullable|string|max:255',
            'tipe_bisnis'     => 'nullable|string|max:100',
            'ditemukan_tahun' => 'nullable|integer|min:1800|max:2100',
            'deskripsi'       => 'nullable|string',
        ]);

        $existing = DB::selectOne("SELECT id FROM perusahaan WHERE recruiter_id = ?", [$uid]);

        if ($existing) {
            DB::update("
                UPDATE perusahaan SET nama=?, lokasi=?, website=?, tipe_bisnis=?, ditemukan_tahun=?, deskripsi=?
                WHERE recruiter_id = ?
            ", [
                $request->nama, $request->lokasi, $request->website,
                $request->tipe_bisnis, $request->ditemukan_tahun ?: null,
                $request->deskripsi, $uid,
            ]);
        } else {
            DB::insert("
                INSERT INTO perusahaan (recruiter_id, nama, lokasi, website, tipe_bisnis, ditemukan_tahun, deskripsi)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ", [
                $uid, $request->nama, $request->lokasi, $request->website,
                $request->tipe_bisnis, $request->ditemukan_tahun ?: null, $request->deskripsi,
            ]);
        }

        return redirect()->back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}
