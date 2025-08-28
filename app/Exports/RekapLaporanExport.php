<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RekapLaporanExport implements FromView, ShouldAutoSize
{
    protected $dailyRecords;
    protected $mapels;
    protected $selectedMonth;

    public function __construct($dailyRecords, $mapels, $selectedMonth)
    {
        $this->dailyRecords = $dailyRecords;
        $this->mapels = $mapels;
        $this->selectedMonth = $selectedMonth;
    }

    public function view(): View
    {
        return view('admin.rekap.excel', [
            'dailyRecords' => $this->dailyRecords,
            'mapels' => $this->mapels,
            'selectedMonth' => $this->selectedMonth
        ]);
    }
}
