<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        // ngambil data statistik buat ditampilin di halaman home
        $totalCompanies = DB::selectOne("SELECT COUNT(*) AS c FROM perusahaan")->c;
        $totalJobs      = DB::selectOne("SELECT COUNT(*) AS c FROM jobs")->c;
        $totalUsers     = DB::selectOne("SELECT COUNT(*) AS c FROM users WHERE role = 'user'")->c;

        // ambil 4 job terbaru
        $recentJobs = DB::select("
            SELECT j.id, j.nama_posisi, j.nama_perusahaan, j.lokasi, j.tipe, j.kategori, j.created_at,
                COUNT(l.id) AS total_pelamar
            FROM jobs j
            LEFT JOIN lamaran l ON l.job_id = j.id
            GROUP BY j.id
            ORDER BY j.created_at DESC
            LIMIT 4
        ");

        // ambil 2 review terbaru
        $recentReviews = DB::select("
            SELECT r.rating, r.isi_review, r.nama_perusahaan, u.nama AS reviewer
            FROM reviews r
            LEFT JOIN users u ON u.id = r.user_id
            ORDER BY r.created_at DESC
            LIMIT 2
        ");

        $tipeMap = config('tipe_map');
        foreach ($recentJobs as $job) {
            $map = $tipeMap[$job->tipe] ?? ['class' => '', 'label' => ucfirst($job->tipe)];
            $job->tipe_class = $map['class'];
            $job->tipe_label = $map['label'];
        }

        return view('home', compact('totalCompanies', 'totalJobs', 'totalUsers', 'recentJobs', 'recentReviews'));
    }
}
