<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    // === halaman skill matching (form input) ===
    public function index()
    {
        $userId = session('user_id');

        // ambil skill dari profil user buat di-preload ke form
        $userSkills = DB::select("
            SELECT s.nama, s.kategori
            FROM user_skills us
            JOIN skills s ON s.id = us.skill_id
            WHERE us.user_id = ?
            ORDER BY s.kategori, s.nama
        ", [$userId]);

        // cek apakah user punya CV yang udah di-parse buat fitur AI matching
        $hasCV = DB::selectOne('SELECT cv, cv_parsed FROM users WHERE id = ?', [$userId]);

        return view('matching', [
            'userSkills' => $userSkills,
            'hasCV'      => $hasCV && $hasCV->cv && $hasCV->cv_parsed,
        ]);
    }

    // === halaman hasil matching ===
    public function hasil(Request $request)
    {
        $userId = session('user_id');

        // ambil skill dari input form (dipisah koma)
        $skillInput = $request->input('skills', '');
        $extraSkills = array_filter(array_map('trim', explode(',', $skillInput)));

        // ambil juga skill dari profil user
        $profileSkills = DB::select("
            SELECT s.nama, s.kategori
            FROM user_skills us
            JOIN skills s ON s.id = us.skill_id
            WHERE us.user_id = ?
        ", [$userId]);

        // gabungin semua skill: dari profil + dari input, buang yang dobel
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
                // coba cari kategorinya dari db
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

        // ambil semua job yang aktif
        $jobs = DB::select("
            SELECT id, nama_posisi, nama_perusahaan, lokasi, tipe, kategori,
                   deskripsi, requirement, gaji_min, gaji_max, created_at
            FROM jobs
            ORDER BY created_at DESC
        ");

        // === algoritma ngitung skor kecocokan ===
        $scoredJobs = [];
        $skillMatchCount = []; // nyimpen berapa banyak job yang cocok per skill

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

                // cocokkan kategori skill dengan kategori job
                if (isset($skillCategories[$skLower]) && $skillCategories[$skLower]) {
                    if (mb_strtolower($skillCategories[$skLower]) === $jobKategori) {
                        $score += 30;
                        $matched = true;
                    }
                }

                // cek apakah nama skill muncul di deskripsi atau requirement job
                if (mb_strpos($jobText, $skLower) !== false) {
                    $score += 15;
                    $matched = true;
                }

                if ($matched) {
                    $matchedSkills[] = $sk;
                    $skillMatchCount[$skLower]++;
                }
            }

            // normalisasi skor ke persen, max = 30 (kategori) + 15 * jumlah skill
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

        // urutkan dari yang paling cocok, ambil top 10
        usort($scoredJobs, fn($a, $b) => $b['match_percent'] <=> $a['match_percent']);
        $topJobs = array_slice($scoredJobs, 0, 10);

        // skor keseluruhan = rata-rata dari top 5
        $topForAvg = array_slice($topJobs, 0, 5);
        $matchScore = count($topForAvg) > 0
            ? round(array_sum(array_column($topForAvg, 'match_percent')) / count($topForAvg))
            : 0;

        // analisis skill: skill mana yang paling banyak cocok sama job
        arsort($skillMatchCount);
        $skillAnalysis = [];
        $maxJobs = count($jobs) ?: 1;
        foreach ($skillMatchCount as $sk => $cnt) {
            // nyari nama aslinya (biar capitalization-nya bener)
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

        // skill yang cocok 3+ job = kelebihan, yang 0 = perlu ditingkatkan
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

    // === AI job matching pakai OpenRouter LLM ===
    public function aiMatch()
    {
        $userId = session('user_id');

        // ambil CV yang udah di-parse dari profil user
        $user = DB::selectOne('
            SELECT nama, pendidikan, jurusan, cv_parsed, bio
            FROM users WHERE id = ?
        ', [$userId]);

        if (!$user || !$user->cv_parsed) {
            return redirect()->route('matching')->withErrors([
                'ai' => 'Upload CV terlebih dahulu di halaman Profil untuk menggunakan AI Matching.'
            ]);
        }

        // ambil skill user
        $skills = DB::select("
            SELECT s.nama, us.level
            FROM user_skills us
            JOIN skills s ON s.id = us.skill_id
            WHERE us.user_id = ?
        ", [$userId]);

        $skillList = array_map(fn($s) => $s->nama . ' (' . $s->level . ')', $skills);

        // ambil job-job yang aktif
        $jobs = DB::select("
            SELECT id, nama_posisi, nama_perusahaan, lokasi, tipe, kategori,
                   deskripsi, requirement, gaji_min, gaji_max
            FROM jobs
            ORDER BY created_at DESC
            LIMIT " . config('openrouter.max_jobs', 20)
        );

        if (empty($jobs)) {
            return view('hasil_ai', [
                'aiResults'  => [],
                'error'      => 'Belum ada lowongan aktif tersedia.',
                'user'       => $user,
            ]);
        }

        // bikin teks deskripsi job buat dikirim ke AI
        $jobTexts = [];
        foreach ($jobs as $i => $j) {
            $jobTexts[] = sprintf(
                "[%d] ID:%d | %s at %s | Location: %s | Type: %s | Category: %s\nDescription: %s\nRequirements: %s",
                $i + 1, $j->id, $j->nama_posisi, $j->nama_perusahaan,
                $j->lokasi ?? 'N/A', $j->tipe ?? 'N/A', $j->kategori ?? 'N/A',
                mb_substr($j->deskripsi ?? 'N/A', 0, 300),
                mb_substr($j->requirement ?? 'N/A', 0, 300)
            );
        }

        // bangun prompt buat dikirim ke AI
        $cvText = mb_substr($user->cv_parsed, 0, 4000); // potong di 4000 karakter
        $prompt = $this->buildPrompt($user, $skillList, $cvText, $jobTexts);

        // panggil API OpenRouter
        $result = $this->callOpenRouter($prompt);

        if ($result === null) {
            return view('hasil_ai', [
                'aiResults'  => [],
                'error'      => 'AI gagal menganalisis. Silakan coba lagi.',
                'user'       => $user,
            ]);
        }

        // parse hasil dari AI
        $aiResults = $this->parseAIResponse($result, $jobs);

        return view('hasil_ai', [
            'aiResults'  => $aiResults,
            'error'      => null,
            'user'       => $user,
        ]);
    }

    // === bangun prompt buat AI ===
    private function buildPrompt($user, $skillList, $cvText, $jobTexts)
    {
        $skillsStr = !empty($skillList) ? implode(', ', $skillList) : 'No skills listed in profile';

        $systemMsg = <<<PROMPT
You are an expert HR recruiter and career advisor for an Indonesian job platform called DoleKerjo.
Your task is to match a candidate's CV with available job listings.

EVALUATION CRITERIA:
1. SKILL MATCH: Does the candidate have the skills required for the job?
2. EXPERIENCE: Does the candidate have relevant work experience? If they have the skills but ZERO relevant experience, score lower.
3. EDUCATION FIT: Does their education level match the job requirements?
4. OVERALL SUITABILITY: Consider all factors holistically.

SCORING (0-100):
- 80-100: Strong match (skills + experience + education align well)
- 60-79: Good match (has skills and some relevant background)
- 40-59: Moderate match (has some skills but lacks experience or education fit)
- 20-39: Weak match (significant gaps)
- 0-19: Not recommended

IMPORTANT: Even if a candidate lists a skill, if their CV shows NO experience using that skill in a work/professional context, penalize the score.

LANGUAGE: All "reasoning", "matched_skills", and "gaps" text MUST be written in Bahasa Indonesia (Indonesian language).

Respond ONLY with valid JSON. No markdown, no explanation, no code blocks. Just the JSON array.
PROMPT;

        $userMsg = <<<USER
CANDIDATE PROFILE:
Name: {$user->nama}
Education: {$user->pendidikan} {$user->jurusan}
Bio: {$user->bio}
Skills: {$skillsStr}

CV CONTENT (parsed markdown):
{$cvText}

AVAILABLE JOBS:
{$this->joinJobTexts($jobTexts)}

Return a JSON array with ONLY jobs scoring 30 or above, sorted by score descending (max 10 jobs).
Format:
[
  {
    "job_id": <number>,
    "score": <0-100>,
    "reasoning": "<2-3 kalimat dalam Bahasa Indonesia menjelaskan mengapa pekerjaan ini cocok atau tidak>",
    "matched_skills": ["skill1", "skill2"],
    "gaps": ["gap1", "gap2"]
  }
]
USER;

        return [
            ['role' => 'system', 'content' => $systemMsg],
            ['role' => 'user',   'content' => $userMsg],
        ];
    }

    private function joinJobTexts(array $jobTexts): string
    {
        return implode("\n\n", $jobTexts);
    }

    // === manggil API OpenRouter, ada retry kalau gagal ===
    private function callOpenRouter(array $messages): ?string
    {
        $apiKey = config('openrouter.api_key');
        if (!$apiKey) {
            Log::error('OpenRouter API key not configured');
            return null;
        }

        // model utama + fallback kalau rate limit
        $models = [
            config('openrouter.model'),
            'meta-llama/llama-3.3-70b-instruct:free',
            'nousresearch/hermes-3-llama-3.1-405b:free',
        ];
        $models = array_unique($models);

        foreach ($models as $model) {
            $result = $this->doApiCall($apiKey, $model, $messages);
            if ($result !== null) {
                return $result;
            }
            // kalau kena rate limit, tunggu bentar terus coba model berikutnya
            usleep(500000); // delay 0.5 detik
        }

        return null;
    }

    private function doApiCall(string $apiKey, string $model, array $messages): ?string
    {
        $payload = [
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => config('openrouter.temperature', 0.2),
            'max_tokens'  => 2000,
        ];

        $ch = curl_init(config('openrouter.base_url'));
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => config('openrouter.timeout', 60),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
                'HTTP-Referer: http://localhost:8000',
                'X-Title: DoleKerjo AI Matching',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('OpenRouter cURL error: ' . $curlError);
            return null;
        }

        if ($httpCode === 429) {
            Log::warning("OpenRouter rate-limited on model: $model");
            return null; // coba model lain
        }

        if ($httpCode !== 200) {
            Log::error('OpenRouter API error (' . $httpCode . '): ' . $response);
            return null;
        }

        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? null;
    }

    // === parse response JSON dari AI ===
    private function parseAIResponse(string $raw, array $jobs): array
    {
        // bersihin kalau ada markdown code block yang nyangkut
        $clean = preg_replace('/```(?:json)?\s*/i', '', $raw);
        $clean = trim($clean);

        $parsed = json_decode($clean, true);
        if (!is_array($parsed)) {
            // coba ekstrak JSON dari response kalau gagal parse langsung
            if (preg_match('/\[.*\]/s', $clean, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
        }

        if (!is_array($parsed)) {
            return [];
        }

        // bikin lookup job berdasarkan id biar gampang dicari
        $jobMap = [];
        foreach ($jobs as $j) {
            $jobMap[$j->id] = $j;
        }

        $results = [];
        foreach ($parsed as $item) {
            $jobId = $item['job_id'] ?? null;
            if (!$jobId || !isset($jobMap[$jobId])) continue;

            $j = $jobMap[$jobId];
            $results[] = [
                'id'              => $j->id,
                'nama_posisi'     => $j->nama_posisi,
                'nama_perusahaan' => $j->nama_perusahaan,
                'lokasi'          => $j->lokasi,
                'tipe'            => $j->tipe,
                'kategori'        => $j->kategori,
                'gaji_min'        => $j->gaji_min,
                'gaji_max'        => $j->gaji_max,
                'match_percent'   => min(max(intval($item['score'] ?? 0), 0), 100),
                'reasoning'       => $item['reasoning'] ?? '',
                'matched_skills'  => $item['matched_skills'] ?? [],
                'gaps'            => $item['gaps'] ?? [],
            ];
        }

        // urutkan dari skor tertinggi
        usort($results, fn($a, $b) => $b['match_percent'] <=> $a['match_percent']);

        return array_slice($results, 0, 10);
    }
}
