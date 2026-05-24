<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
   public function index()
{
    if (!session('user_id')) return redirect()->route('login');

    $jobs = DB::select('SELECT * FROM jobs ORDER BY created_at DESC');

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

    return view('jobs', compact('jobs'));
}

    public function show($id)
{
    $job = DB::selectOne('SELECT * FROM jobs WHERE id = ?', [$id]);
    if (!$job) abort(404);
    return response()->json($job);
}


}