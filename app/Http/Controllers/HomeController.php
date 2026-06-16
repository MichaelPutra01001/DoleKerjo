<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (!session('user_id')) return redirect()->route('login');

        $userId = session('user_id');

        // Stats
        $totalCompanies = DB::selectOne("SELECT COUNT(*) AS c FROM perusahaan")->c;
        $totalJobs      = DB::selectOne("SELECT COUNT(*) AS c FROM jobs")->c;
        $totalUsers     = DB::selectOne("SELECT COUNT(*) AS c FROM users WHERE role = 'user'")->c;

        // 4 recent jobs
        $recentJobs = DB::select("
            SELECT j.id, j.nama_posisi, j.nama_perusahaan, j.lokasi, j.tipe, j.kategori, j.created_at,
                COUNT(l.id) AS total_pelamar
            FROM jobs j
            LEFT JOIN lamaran l ON l.job_id = j.id
            GROUP BY j.id
            ORDER BY j.created_at DESC
            LIMIT 4
        ");

        // 2 recent reviews
        $recentReviews = DB::select("
            SELECT r.rating, r.isi_review, r.nama_perusahaan, u.nama AS reviewer
            FROM reviews r
            LEFT JOIN users u ON u.id = r.user_id
            ORDER BY r.created_at DESC
            LIMIT 2
        ");

        $tipeMap = [
            'full-time'   => ['class' => '',        'label' => 'Full Time'],
            'part-time'   => ['class' => 'parttime', 'label' => 'Part Time'],
            'remote'      => ['class' => 'remote',   'label' => 'Remote'],
            'hybrid'      => ['class' => 'hybrid',   'label' => 'Hybrid'],
            'contract'    => ['class' => 'contract', 'label' => 'Contract'],
            'partnership' => ['class' => 'partner',  'label' => 'Partnership'],
        ];
        foreach ($recentJobs as $job) {
            $map = $tipeMap[$job->tipe] ?? ['class' => '', 'label' => ucfirst($job->tipe)];
            $job->tipe_class = $map['class'];
            $job->tipe_label = $map['label'];
        }

        return view('home', compact('totalCompanies', 'totalJobs', 'totalUsers', 'recentJobs', 'recentReviews'));
    }
}