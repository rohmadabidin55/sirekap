<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruAsuh extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guru_asuh';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * Get the guru that owns the assignment.
     */
    public function guru()
    {
        // Relasi ke Guru (dengan data user-nya)
        return $this->belongsTo(Guru::class)->with('user');
    }

    /**
     * Get the siswa that owns the assignment.
     */
    public function siswa()
    {
        // Relasi ke Siswa (dengan data user dan kelas-nya)
        return $this->belongsTo(Siswa::class)->with(['user', 'kelas']);
    }
}
