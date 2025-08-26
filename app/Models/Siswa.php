<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke model User (satu data siswa milik satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke model Kelas (satu siswa milik satu kelas)
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi ke model Nilai (satu siswa punya banyak nilai)
    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }

    // Relasi ke model Presensi (satu siswa punya banyak data presensi)
    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    /**
     * PERBAIKAN: Tambahkan relasi ini.
     * Mendefinisikan relasi bahwa satu siswa memiliki satu guru asuh.
     */
    public function guruAsuh()
    {
        return $this->hasOne(GuruAsuh::class);
    }
}
