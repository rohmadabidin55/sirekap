<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $guarded = ['id'];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    /**
     * PERBAIKAN: Tambahkan relasi ini.
     * Mendefinisikan bahwa satu kelas memiliki banyak penugasan guru & mapel.
     */
    public function guruMataPelajaranAssignments()
    {
        return $this->hasMany(GuruMataPelajaran::class, 'kelas_id');
    }
}
