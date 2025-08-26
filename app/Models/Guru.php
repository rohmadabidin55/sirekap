<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke model User (satu data guru milik satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke model MataPelajaran
    public function mataPelajarans()
    {
        return $this->belongsToMany(MataPelajaran::class, 'guru_mata_pelajaran');
    }

    // Relasi ke model Nilai (satu guru bisa memberi banyak nilai)
    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }

    // Relasi ke model Presensi (satu guru bisa mencatat banyak presensi)
    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }
}
