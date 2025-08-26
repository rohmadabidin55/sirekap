<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke model Guru (satu mapel bisa diajar banyak guru, dan sebaliknya)
    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_mata_pelajaran');
    }
}

