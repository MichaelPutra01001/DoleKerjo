<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

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

        $p = DB::selectOne("SELECT * FROM perusahaan WHERE id = ?", [$id]);
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
            // User / Admin: lihat semua job + jumlah lamaran
            $jobs = DB::select("
                SELECT j.*,
                    COUNT(l.id) AS total_lamaran
                FROM jobs j
                LEFT JOIN lamaran l ON l.job_id = j.id
                WHERE j.nama_perusahaan = ?
                GROUP BY j.id
                ORDER BY j.created_at DESC
            ", [$p->nama]);
        }

        return response()->json([
            'jobs'       => $jobs,
            'role'       => $role,
            'recruiter_id' => $p->recruiter_id,
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
