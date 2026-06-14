<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    // ─── Halaman daftar perusahaan ────────────────────────────────────────────
    public function index()
    {
        if (!session('user_id')) return redirect()->route('login');

        $perusahaan = DB::select("
            SELECT
                p.*,
                u.nama  AS recruiter_nama,
                COUNT(DISTINCT j.id)  AS total_jobs,
                ROUND(AVG(r.rating), 1) AS avg_rating,
                COUNT(DISTINCT r.id)  AS total_reviews
            FROM perusahaan p
            LEFT JOIN users u ON u.id = p.recruiter_id
            LEFT JOIN jobs  j ON j.nama_perusahaan = p.nama
            LEFT JOIN reviews r ON r.nama_perusahaan = p.nama
            GROUP BY p.id
            ORDER BY p.nama ASC
        ");

        return view('perusahaan.index', [
            'perusahaan' => $perusahaan,
            'role'       => session('role', 'user'),
            'user_id'    => session('user_id'),
        ]);
    }

    // ─── Halaman detail perusahaan (shell, data subtab di-load via AJAX) ─────
    public function show($id)
    {
        if (!session('user_id')) return redirect()->route('login');

        $p = DB::selectOne("
            SELECT p.*, u.nama AS recruiter_nama
            FROM perusahaan p
            LEFT JOIN users u ON u.id = p.recruiter_id
            WHERE p.id = ?
        ", [$id]);

        if (!$p) abort(404);

        return view('perusahaan.show', [
            'perusahaan' => $p,
            'role'       => session('role', 'user'),
            'user_id'    => session('user_id'),
        ]);
    }

    // ─── API: Overview ────────────────────────────────────────────────────────
    public function getOverview($id)
    {
        if (!session('user_id')) return response()->json(['error' => 'Unauthenticated'], 401);

        $p = DB::selectOne("
            SELECT p.*, u.nama AS recruiter_nama
            FROM perusahaan p
            LEFT JOIN users u ON u.id = p.recruiter_id
            WHERE p.id = ?
        ", [$id]);
        if (!$p) return response()->json(['error' => 'Not found'], 404);

        // Statistik review
        $reviewStats = DB::selectOne("
            SELECT
                COUNT(*)            AS total,
                ROUND(AVG(rating),1) AS avg_rating,
                COUNT(CASE WHEN rating = 5 THEN 1 END) AS bintang5,
                COUNT(CASE WHEN rating = 4 THEN 1 END) AS bintang4,
                COUNT(CASE WHEN rating = 3 THEN 1 END) AS bintang3,
                COUNT(CASE WHEN rating = 2 THEN 1 END) AS bintang2,
                COUNT(CASE WHEN rating = 1 THEN 1 END) AS bintang1
            FROM reviews WHERE nama_perusahaan = ?
        ", [$p->nama]);

        // 2 review terbaru
        $recentReviews = DB::select("
            SELECT r.rating, r.posisi_user, r.isi_review, r.created_at, u.nama AS reviewer
            FROM reviews r
            LEFT JOIN users u ON u.id = r.user_id
            WHERE r.nama_perusahaan = ?
            ORDER BY r.created_at DESC LIMIT 2
        ", [$p->nama]);

        // Total job aktif
        $totalJobs = DB::selectOne("
            SELECT COUNT(*) AS total FROM jobs WHERE nama_perusahaan = ?
        ", [$p->nama]);

        return response()->json([
            'perusahaan'    => $p,
            'review_stats'  => $reviewStats,
            'recent_reviews'=> $recentReviews,
            'total_jobs'    => $totalJobs->total ?? 0,
        ]);
    }

    // ─── API: Review ──────────────────────────────────────────────────────────
    public function getReviews($id)
    {
        if (!session('user_id')) return response()->json(['error' => 'Unauthenticated'], 401);

        $p = DB::selectOne("SELECT nama FROM perusahaan WHERE id = ?", [$id]);
        if (!$p) return response()->json(['error' => 'Not found'], 404);

        $reviews = DB::select("
            SELECT r.*, u.nama AS reviewer_nama, u.foto_profil
            FROM reviews r
            LEFT JOIN users u ON u.id = r.user_id
            WHERE r.nama_perusahaan = ?
            ORDER BY r.created_at DESC
        ", [$p->nama]);

        return response()->json($reviews);
    }

    // ─── API: Lamaran (daftar job dari perusahaan ini) ────────────────────────
    public function getLamaran($id)
    {
        if (!session('user_id')) return response()->json(['error' => 'Unauthenticated'], 401);

        $p = DB::selectOne("SELECT * FROM perusahaan WHERE id = ?", [$id]);
        if (!$p) return response()->json(['error' => 'Not found'], 404);

        $role   = session('role', 'user');
        $userId = session('user_id');

        if ($role === 'recruiter') {
            // Recruiter hanya melihat job miliknya + total lamaran per job
            $jobs = DB::select("
                SELECT j.*,
                    COUNT(l.id) AS total_lamaran
                FROM jobs j
                LEFT JOIN lamaran l ON l.job_id = j.id
                WHERE j.nama_perusahaan = ? AND j.recruiter_id = ?
                GROUP BY j.id
                ORDER BY j.created_at DESC
            ", [$p->nama, $userId]);
        } else {
            // User: lihat semua job + cek apakah user sudah melamar
            $jobs = DB::select("
                SELECT j.*,
                    COUNT(l_all.id) AS total_lamaran,
                    MAX(CASE WHEN l_user.id IS NOT NULL THEN 1 ELSE 0 END) AS sudah_lamar
                FROM jobs j
                LEFT JOIN lamaran l_all ON l_all.job_id = j.id
                LEFT JOIN lamaran l_user ON l_user.job_id = j.id AND l_user.user_id = ?
                WHERE j.nama_perusahaan = ?
                GROUP BY j.id
                ORDER BY j.created_at DESC
            ", [$userId, $p->nama]);
        }

        return response()->json([
            'jobs'         => $jobs,
            'role'         => $role,
            'recruiter_id' => $p->recruiter_id,
            'user_id'      => $userId,
        ]);
    }

    // ─── User: Lamar pekerjaan ─────────────────────────────────────────────────
    public function applyJob(Request $request)
    {
        if (!session('user_id')) return response()->json(['error' => 'Unauthenticated'], 401);
        if (session('role') !== 'user') return response()->json(['error' => 'Hanya user yang bisa melamar'], 403);

        $jobId  = $request->input('job_id');
        $userId = session('user_id');

        // Cek apakah job ada
        $job = DB::selectOne("SELECT * FROM jobs WHERE id = ?", [$jobId]);
        if (!$job) return response()->json(['error' => 'Lowongan tidak ditemukan'], 404);

        // Cek apakah sudah pernah melamar
        $existing = DB::selectOne("SELECT id FROM lamaran WHERE user_id = ? AND job_id = ?", [$userId, $jobId]);
        if ($existing) return response()->json(['error' => 'Anda sudah melamar pekerjaan ini'], 400);

        DB::insert("INSERT INTO lamaran (user_id, job_id, status) VALUES (?, ?, 'pending')", [$userId, $jobId]);

        return response()->json(['success' => true, 'message' => 'Lamaran berhasil dikirim!']);
    }

    // ─── User: Simpan review + rating ───────────────────────────────────────────
    public function storeReview(Request $request)
    {
        if (!session('user_id')) return response()->json(['error' => 'Unauthenticated'], 401);

        $userId   = session('user_id');
        $namaPrsh = $request->input('nama_perusahaan');
        $rating   = (int) $request->input('rating');
        $posisi   = $request->input('posisi_user') ?: null;
        $isi      = $request->input('isi_review');

        // Validasi
        if (!$namaPrsh)  return response()->json(['error' => 'Nama perusahaan kosong'], 400);
        if ($rating < 1 || $rating > 5) return response()->json(['error' => 'Rating harus 1-5'], 400);
        if (!$isi || strlen(trim($isi)) < 3) return response()->json(['error' => 'Review minimal 3 karakter'], 400);

        // Cek apakah user sudah pernah review perusahaan ini
        $existing = DB::selectOne("SELECT id FROM reviews WHERE user_id = ? AND nama_perusahaan = ?", [$userId, $namaPrsh]);
        if ($existing) {
            // Update review lama
            DB::update("
                UPDATE reviews SET rating = ?, posisi_user = ?, isi_review = ?
                WHERE id = ?
            ", [$rating, $posisi, $isi, $existing->id]);
        } else {
            DB::insert("
                INSERT INTO reviews (user_id, nama_perusahaan, posisi_user, isi_review, rating)
                VALUES (?, ?, ?, ?, ?)
            ", [$userId, $namaPrsh, $posisi, $isi, $rating]);
        }

        // Return avg rating terbaru
        $stats = DB::selectOne("
            SELECT COUNT(*) AS total, ROUND(AVG(rating),1) AS avg_rating
            FROM reviews WHERE nama_perusahaan = ?
        ", [$namaPrsh]);

        return response()->json([
            'success'    => true,
            'message'    => $existing ? 'Review berhasil diperbarui!' : 'Review berhasil ditambahkan!',
            'avg_rating' => $stats->avg_rating,
            'total'      => $stats->total,
        ]);
    }

    // ─── API: Connections ─────────────────────────────────────────────────────
    public function getConnections($id)
    {
        if (!session('user_id')) return response()->json(['error' => 'Unauthenticated'], 401);

        $p = DB::selectOne("SELECT * FROM perusahaan WHERE id = ?", [$id]);
        if (!$p) return response()->json(['error' => 'Not found'], 404);

        // Cek apakah tabel connections sudah ada
        $tableExists = DB::select("SHOW TABLES LIKE 'perusahaan_connections'");
        if (empty($tableExists)) {
            return response()->json(['connections' => [], 'perusahaan' => $p, 'note' => 'table_not_ready']);
        }

        $connections = DB::select("
            SELECT pc.id, pc.tipe, pc.catatan, pc.created_at,
                p2.id AS connected_id, p2.nama AS connected_nama,
                p2.logo AS connected_logo, p2.lokasi AS connected_lokasi,
                p2.website AS connected_website
            FROM perusahaan_connections pc
            JOIN perusahaan p2 ON p2.id = pc.connected_to
            WHERE pc.perusahaan_id = ?
            UNION
            SELECT pc.id, pc.tipe, pc.catatan, pc.created_at,
                p2.id AS connected_id, p2.nama AS connected_nama,
                p2.logo AS connected_logo, p2.lokasi AS connected_lokasi,
                p2.website AS connected_website
            FROM perusahaan_connections pc
            JOIN perusahaan p2 ON p2.id = pc.perusahaan_id
            WHERE pc.connected_to = ?
            ORDER BY connected_nama ASC
        ", [$id, $id]);

        return response()->json([
            'connections' => $connections,
            'perusahaan'  => $p,
            'role'        => session('role', 'user'),
        ]);
    }
}
