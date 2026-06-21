<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $userId = session('user_id');

        // ambil parameter filter dari request
        $sort     = $request->input('sort', 'terbaru');
        $kategori = $request->input('kategori', []);   // array kategori yang dipilih
        $tipe     = $request->input('tipe', []);        // array tipe yang dipilih
        $lokasi   = $request->input('lokasi', []);      // array lokasi yang dipilih

        // tentuin urutan sortir
        $orderBy = match($sort) {
            'gaji'      => 'j.gaji_max DESC',
            default     => 'j.created_at DESC',  // default terbaru
        };

        // bangun kondisi WHERE
        $where = [];
        $binds = [];

        if (!empty($kategori)) {
            $placeholders = implode(',', array_fill(0, count($kategori), '?'));
            $where[] = "j.kategori IN ($placeholders)";
            $binds = array_merge($binds, $kategori);
        }

        if (!empty($tipe)) {
            $placeholders = implode(',', array_fill(0, count($tipe), '?'));
            $where[] = "j.tipe IN ($placeholders)";
            $binds = array_merge($binds, $tipe);
        }

        if (!empty($lokasi)) {
            $lokasiKondisi = [];
            foreach ($lokasi as $l) {
                if (strtolower($l) === 'remote') {
                    $lokasiKondisi[] = "j.tipe = 'remote'";
                } else {
                    $lokasiKondisi[] = "j.lokasi LIKE ?";
                    $binds[] = '%' . $l . '%';
                }
            }
            $where[] = '(' . implode(' OR ', $lokasiKondisi) . ')';
        }

        $whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $jobs = DB::select("
            SELECT j.*,
                COUNT(l.id) AS total_pelamar,
                MAX(CASE WHEN l_user.id IS NOT NULL THEN 1 ELSE 0 END) AS sudah_lamar
            FROM jobs j
            LEFT JOIN lamaran l ON l.job_id = j.id
            LEFT JOIN lamaran l_user ON l_user.job_id = j.id AND l_user.user_id = ?
            {$whereSQL}
            GROUP BY j.id
            ORDER BY {$orderBy}
        ", array_merge([$userId], $binds));

        // mapping tipe ke label dan class css
        $tipeMap = config('tipe_map');

        foreach ($jobs as $job) {
            $map = $tipeMap[$job->tipe] ?? ['class' => '', 'label' => ucfirst($job->tipe)];
            $job->tipe_class = $map['class'];
            $job->tipe_label = $map['label'];
        }

        // ambil semua lokasi unik buat filter
        $locations = DB::select("SELECT DISTINCT lokasi FROM jobs WHERE lokasi IS NOT NULL AND lokasi != '' ORDER BY lokasi ASC");

        // ambil kategori dinamis dari db, yang paling banyak jobnya duluan
        $categories = DB::select("
            SELECT k.nama,
                (SELECT COUNT(*) FROM jobs WHERE kategori = k.nama) AS job_count
            FROM kategori k
            ORDER BY job_count DESC, k.nama ASC
        ");

        return view('jobs', compact('jobs', 'sort', 'kategori', 'tipe', 'lokasi', 'locations', 'categories'));
    }

    public function show($id)
    {
        $userId = session('user_id');

        $job = DB::selectOne("
            SELECT j.*,
                COUNT(l.id) AS total_pelamar,
                MAX(CASE WHEN l_user.id IS NOT NULL THEN 1 ELSE 0 END) AS sudah_lamar
            FROM jobs j
            LEFT JOIN lamaran l ON l.job_id = j.id
            LEFT JOIN lamaran l_user ON l_user.job_id = j.id AND l_user.user_id = ?
            WHERE j.id = ?
            GROUP BY j.id
        ", [$userId, $id]);

        if (!$job) abort(404);

        // mapping tipe ke label dan class
        $tipeMap = config('tipe_map');
        $map = $tipeMap[$job->tipe] ?? ['class' => '', 'label' => ucfirst($job->tipe)];
        $job->tipe_class = $map['class'];
        $job->tipe_label = $map['label'];

        // ambil info perusahaan kalau ada
        $perusahaan = DB::selectOne("
            SELECT p.*, u.nama AS recruiter_nama
            FROM perusahaan p
            LEFT JOIN users u ON u.id = p.recruiter_id
            WHERE p.nama = ?
            LIMIT 1
        ", [$job->nama_perusahaan]);

        // job lain dari perusahaan yang sama, max 5
        $relatedJobs = DB::select("
            SELECT id, nama_posisi, lokasi, tipe, created_at
            FROM jobs
            WHERE nama_perusahaan = ? AND id != ?
            ORDER BY created_at DESC
            LIMIT 5
        ", [$job->nama_perusahaan, $job->id]);

        return view('job_detail', compact('job', 'perusahaan', 'relatedJobs'));
    }

    // endpoint JSON buat modal, masih dipakai
    public function data($id)
    {
        $job = DB::selectOne('SELECT * FROM jobs WHERE id = ?', [$id]);
        if (!$job) return response()->json(['error' => 'Not found'], 404);
        return response()->json($job);
    }
}
