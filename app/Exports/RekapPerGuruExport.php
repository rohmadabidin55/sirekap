<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RekapPerGuruExport implements FromView, ShouldAutoSize
{
    protected $siswas;
    protected $nilaiHarian;
    protected $rataRataNilai;
    protected $daysInMonth;
    protected $selectedMonth;
    protected $guru;
    protected $mapel;

    public function __construct($siswas, $nilaiHarian, $rataRataNilai, $daysInMonth, $selectedMonth, $guru, $mapel)
    {
        $this->siswas = $siswas;
        $this->nilaiHarian = $nilaiHarian;
        $this->rataRataNilai = $rataRataNilai;
        $this->daysInMonth = $daysInMonth;
        $this->selectedMonth = $selectedMonth;
        $this->guru = $guru;
        $this->mapel = $mapel;
    }

    public function view(): View
    {
        return view('admin.rekappergu.excel', [
            'siswas' => $this->siswas,
            'nilaiHarian' => $this->nilaiHarian,
            'rataRataNilai' => $this->rataRataNilai,
            'daysInMonth' => $this->daysInMonth,
            'selectedMonth' => $this->selectedMonth,
            'guru' => $this->guru,
            'mapel' => $this->mapel,
        ]);
    }
}
