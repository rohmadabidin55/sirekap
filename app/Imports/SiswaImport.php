<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    private $kelas;

    public function __construct()
    {
        // Ambil semua data kelas sekali saja untuk efisiensi
        $this->kelas = Kelas::pluck('id', 'nama_kelas');
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Cari ID kelas berdasarkan nama kelas dari Excel
        $kelasId = $this->kelas->get($row['kelas']);

        // Jika kelas tidak ditemukan, lewati baris ini
        if (!$kelasId) {
            return null;
        }

        // Gunakan transaksi untuk memastikan data konsisten
        return DB::transaction(function () use ($row, $kelasId) {
            // 1. Buat data User
            $user = User::create([
                'name'     => $row['nama_lengkap'],
                'email'    => $row['email'],
                'password' => Hash::make($row['password']),
                'role'     => 'siswa',
            ]);

            // 2. Buat data Siswa yang terhubung
            return new Siswa([
                'user_id' => $user->id,
                'nis'     => $row['nis'],
                'nisn'    => $row['nisn'],
                'kelas_id' => $kelasId,
                'alamat' => $row['alamat'],
                'no_telepon_orang_tua' => $row['no_telepon_orang_tua'],
            ]);
        });
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nis' => 'required|unique:siswas,nis',
            'nisn' => 'nullable|unique:siswas,nisn',
            'kelas' => 'required|exists:kelas,nama_kelas',
        ];
    }
}
