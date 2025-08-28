<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            // 1. Buat data User
            $user = User::create([
                'name'     => $row['nama_lengkap'],
                'email'    => $row['email'],
                'password' => Hash::make($row['password']),
                'role'     => 'guru',
            ]);

            // 2. Buat data Guru yang terhubung
            return new Guru([
                'user_id' => $user->id,
                'nip'     => $row['nip'],
                'alamat' => $row['alamat'],
                'no_telepon' => $row['no_telepon'],
            ]);
        });
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nip' => 'nullable|string|max:50|unique:gurus,nip',
        ];
    }
}
