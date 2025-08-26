<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    protected $guarded = ['id']; // Melindungi field id agar tidak bisa diisi manual

    // Relasi ke model Kelas (satu jurusan punya banyak kelas)
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}

