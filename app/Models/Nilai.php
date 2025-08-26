<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke model Siswa (satu nilai milik satu siswa)
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke model MataPelajaran (satu nilai untuk satu mapel)
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    // Relasi ke model Guru (satu nilai diinput oleh satu guru)
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}

