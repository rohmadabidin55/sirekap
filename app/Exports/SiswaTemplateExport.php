<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaTemplateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Kita hanya butuh header, jadi koleksinya kita kosongkan
        return collect([]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Header ini harus cocok dengan yang ada di file SiswaImport.php
        return [
            'nama_lengkap',
            'email',
            'password',
            'nis',
            'nisn',
            'kelas',
            'alamat',
            'no_telepon_orang_tua',
        ];
    }
}
