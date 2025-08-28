<?php

namespace App\Exports;

use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SiswaExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        return view('admin.siswa.excel', [
            'siswas' => Siswa::with(['user', 'kelas.jurusan'])->get()->sortBy('user.name')
        ]);
    }
}
