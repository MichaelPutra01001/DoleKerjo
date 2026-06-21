<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RecruiterOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_id') || session('role') !== 'recruiter') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Akses ditolak.'], 403);
            }
            abort(403, 'Akses ditolak.');
        }

        // Also enforce is_verified on every request (item #14)
        $userId = session('user_id');
        $user = \Illuminate\Support\Facades\DB::selectOne(
            'SELECT is_verified FROM users WHERE id = ?', [$userId]
        );
        if (!$user || !$user->is_verified) {
            session()->flush();
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Akun rekruter Anda belum diverifikasi.'], 403);
            }
            return redirect()->route('login')->withErrors([
                'login' => 'Akun rekruter Anda belum diverifikasi oleh Admin.',
            ]);
        }

        return $next($request);
    }
}
