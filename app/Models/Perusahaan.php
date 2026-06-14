<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'perusahaan';

    protected $fillable = [
        'recruiter_id', 'nama', 'logo', 'deskripsi', 'lokasi', 'website', 'tipe_bisnis', 'ditemukan_tahun',
    ];
}
