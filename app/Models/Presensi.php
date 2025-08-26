<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;
    protected $table = 'presensis'; // Mendefinisikan nama tabel secara eksplisit
    protected $guarded = ['id'];

    // Relasi ke model Siswa (satu data presensi milik satu siswa)
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke model MataPelajaran (satu presensi untuk satu mapel)
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    // Relasi ke model Guru (satu presensi dicatat oleh satu guru)
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
