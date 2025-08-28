<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GuruTemplateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect([]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'nama_lengkap',
            'email',
            'password',
            'nip',
            'alamat',
            'no_telepon',
        ];
    }
}
