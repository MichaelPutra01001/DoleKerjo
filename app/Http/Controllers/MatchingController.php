<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    // ─── Halaman Skill Matching (form input) ────────────────────────────
    public function index()
    {
        if (!session('user_id')) return redirect()->route('login');

        $userId = session('user_id');

        // Fetch user's profile skills for pre-population
        $userSkills = DB::select("
            SELECT s.nama, s.kategori
            FROM user_skills us
            JOIN skills s ON s.id = us.skill_id
            WHERE us.user_id = ?
            ORDER BY s.kategori, s.nama
        ", [$userId]);

        return view('matching', ['userSkills' => $userSkills]);
    }

    // ─── Halaman Hasil Matching ─────────────────────────────────────────
    public function hasil(Request $request)
    {
        if (!session('user_id')) return redirect()->route('login');

        $userId = session('user_id');

        // Get skill names from form (comma-separated string)
        $skillInput = $request->input('skills', '');
        $extraSkills = array_filter(array_map('trim', explode(',', $skillInput)));

        // Also get user's profile skills
        $profileSkills = DB::select("
            SELECT s.nama, s.kategori
            FROM user_skills us
            JOIN skills s ON s.id = us.skill_id
            WHERE us.user_id = ?
        ", [$userId]);

        // Merge all skills: profile + extra (unique, case-insensitive)
        $allSkills = [];
        $skillCategories = [];
        $seen = [];

        foreach ($profileSkills as $ps) {
            $key = mb_strtolower($ps->nama);
            if (!isset($seen[$key])) {
                $allSkills[] = $ps->nama;
                $skillCategories[$key] = $ps->kategori;
                $seen[$key] = true;
            }
        }
        foreach ($extraSkills as $es) {
            $key = mb_strtolower($es);
            if (!isset($seen[$key]) && strlen($es) > 0) {
                $allSkills[] = $es;
                // Try to find category from DB for extra skills
                $found = DB::selectOne("SELECT kategori FROM skills WHERE LOWER(nama) = ?", [$key]);
                $skillCategories[$key] = $found ? $found->kategori : null;
                $seen[$key] = true;
            }
        }

        if (empty($allSkills)) {
            return view('hasil', [
                'matchScore'    => 0,
                'skillAnalysis' => [],
                'matchedJobs'   => [],
                'strengths'     => [],
                'weaknesses'    => ['Belum ada skill yang dimasukkan. Tambahkan skill di profil atau di form matching.'],
                'totalSkills'   => 0,
            ]);
        }

        // Fetch all active jobs
        $jobs = DB::select("
            SELECT id, nama_posisi, nama_perusahaan, lokasi, tipe, kategori,
                   deskripsi, requirement, gaji_min, gaji_max, created_at
            FROM jobs
            ORDER BY created_at DESC
        ");

        // ── Scoring Algorithm ──
        $scoredJobs = [];
        $skillMatchCount = []; // track how many jobs each skill matches

        foreach ($allSkills as $sk) {
            $skillMatchCount[mb_strtolower($sk)] = 0;
        }

        foreach ($jobs as $job) {
            $score = 0;
            $matchedSkills = [];
            $jobDesc     = mb_strtolower($job->deskripsi ?? '');
            $jobReq      = mb_strtolower($job->requirement ?? '');
            $jobText     = $jobDesc . ' ' . $jobReq;
            $jobKategori = mb_strtolower($job->kategori ?? '');

            foreach ($allSkills as $sk) {
                $skLower = mb_strtolower($sk);
                $matched = false;

                // Category match: skill's kategori matches job's kategori
                if (isset($skillCategories[$skLower]) && $skillCategories[$skLower]) {
                    if (mb_strtolower($skillCategories[$skLower]) === $jobKategori) {
                        $score += 30;
                        $matched = true;
                    }
                }

                // Keyword match: skill name appears in job description/requirement
                if (mb_strpos($jobText, $skLower) !== false) {
                    $score += 15;
                    $matched = true;
                }

                if ($matched) {
                    $matchedSkills[] = $sk;
                    $skillMatchCount[$skLower]++;
                }
            }

            // Normalize: max possible = 30 (cat) + 15 * count(skills)
            $maxScore = 30 + (15 * count($allSkills));
            $percent  = $maxScore > 0 ? min(round(($score / $maxScore) * 100), 100) : 0;

            if ($percent > 0) {
                $scoredJobs[] = [
                    'id'              => $job->id,
                    'nama_posisi'     => $job->nama_posisi,
                    'nama_perusahaan' => $job->nama_perusahaan,
                    'lokasi'          => $job->lokasi,
                    'tipe'            => $job->tipe,
                    'kategori'        => $job->kategori,
                    'gaji_min'        => $job->gaji_min,
                    'gaji_max'        => $job->gaji_max,
                    'match_percent'   => $percent,
                    'matched_skills'  => $matchedSkills,
                ];
            }
        }

        // Sort by match % DESC, take top 10
        usort($scoredJobs, fn($a, $b) => $b['match_percent'] <=> $a['match_percent']);
        $topJobs = array_slice($scoredJobs, 0, 10);

        // Overall match score = average of top 5 (or all if less)
        $topForAvg = array_slice($topJobs, 0, 5);
        $matchScore = count($topForAvg) > 0
            ? round(array_sum(array_column($topForAvg, 'match_percent')) / count($topForAvg))
            : 0;

        // Skill analysis: which skills matched the most jobs
        arsort($skillMatchCount);
        $skillAnalysis = [];
        $maxJobs = count($jobs) ?: 1;
        foreach ($skillMatchCount as $sk => $cnt) {
            // Find original case name
            $displayName = $sk;
            foreach ($allSkills as $as) {
                if (mb_strtolower($as) === $sk) { $displayName = $as; break; }
            }
            $skillAnalysis[] = [
                'name'     => $displayName,
                'matches'  => $cnt,
                'strength' => round(($cnt / $maxJobs) * 100),
                'kategori' => $skillCategories[$sk] ?? null,
            ];
        }

        // Strengths: skills that matched 3+ jobs
        $strengths = [];
        $weaknesses = [];
        foreach ($skillAnalysis as $sa) {
            if ($sa['matches'] >= 3) {
                $strengths[] = $sa['name'] . ' — cocok dengan ' . $sa['matches'] . ' lowongan';
            } elseif ($sa['matches'] === 0) {
                $weaknesses[] = $sa['name'] . ' — belum ada lowongan yang cocok';
            }
        }
        if (empty($strengths) && !empty($topJobs)) {
            $strengths[] = 'Beberapa skill kamu cocok dengan lowongan yang tersedia';
        }
        if (empty($weaknesses)) {
            $weaknesses[] = 'Semua skill kamu memiliki kecocokan dengan lowongan';
        }

        return view('hasil', [
            'matchScore'    => $matchScore,
            'skillAnalysis' => $skillAnalysis,
            'matchedJobs'   => $topJobs,
            'strengths'     => array_slice($strengths, 0, 4),
            'weaknesses'    => array_slice($weaknesses, 0, 4),
            'totalSkills'   => count($allSkills),
        ]);
    }
}
