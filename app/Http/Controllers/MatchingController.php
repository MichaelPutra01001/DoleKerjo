<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchingController extends Controller
{
    public function index()
{
    if (!session('user_id')) return redirect()->route('login');
    return view('matching');
}

public function hasil()
{
    if (!session('user_id')) return redirect()->route('login');
    return view('hasil');
}
}
