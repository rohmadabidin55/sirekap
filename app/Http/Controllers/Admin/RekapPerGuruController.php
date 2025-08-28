<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapPerGuruExport;

class RekapPerGuruController extends Controller
{
    public function index(Request $request)
    {
        // Data untuk filter dropdown
        $gurus = Guru::with('user')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();

        // Inisialisasi variabel
        $siswas = new LengthAwarePaginator([], 0, 10);
        $nilaiHarian = collect();
        $rataRataNilai = collect();
        $daysInMonth = 0;
        $selectedMonth = '';

        // Hanya proses jika semua filter yang diperlukan sudah diisi
        if ($request->filled(['bulan', 'guru_id', 'mapel_id'])) {
            list($siswas, $nilaiHarian, $rataRataNilai, $daysInMonth, $selectedMonth) = $this->fetchRekapData($request, true);
        }

        return view('admin.rekappergu.index', compact(
            'gurus', 'jurusans', 'mapels', 'siswas', 'nilaiHarian', 'rataRataNilai', 'daysInMonth', 'selectedMonth'
        ));
    }

    public function exportExcel(Request $request)
    {
        $guru = Guru::with('user')->find($request->input('guru_id'));
        $mapel = MataPelajaran::find($request->input('mapel_id'));
        $selectedMonth = Carbon::parse($request->input('bulan', now()->format('Y-m')))->locale('id');
        
        list($siswas, $nilaiHarian, $rataRataNilai, $daysInMonth) = $this->fetchRekapData($request, false); // Ambil semua data tanpa pagination

        return Excel::download(new RekapPerGuruExport($siswas, $nilaiHarian, $rataRataNilai, $daysInMonth, $selectedMonth, $guru, $mapel), 'rekap-nilai-'.$guru->user->name.'-'.$mapel->nama_mapel.'-'.$selectedMonth->format('F-Y').'.xlsx');
    }

    private function fetchRekapData(Request $request, bool $paginate)
    {
        $bulan = $request->input('bulan');
        $guru_id = $request->input('guru_id');
        $jurusan_id = $request->input('jurusan_id');
        $mapel_id = $request->input('mapel_id');
        
        $selectedMonth = Carbon::parse($bulan)->locale('id');
        $daysInMonth = $selectedMonth->daysInMonth;

        $kelasQuery = Kelas::query();
        if ($jurusan_id) {
            $kelasQuery->where('jurusan_id', $jurusan_id);
        }
        $kelasQuery->whereHas('guruMataPelajaranAssignments', function ($subQuery) use ($guru_id, $mapel_id) {
            $subQuery->where('guru_id', $guru_id)->where('mata_pelajaran_id', $mapel_id);
        });
        $kelasIds = $kelasQuery->pluck('id');

        $siswaQuery = Siswa::with(['user', 'guruAsuh.guru.user'])
            ->whereIn('kelas_id', $kelasIds)
            ->join('users', 'siswas.user_id', '=', 'users.id')
            ->select('siswas.*')
            ->orderBy('users.name', 'asc');
        
        $siswas = $paginate 
            ? $siswaQuery->paginate($request->input('perPage', 10))->withQueryString()
            : $siswaQuery->get();

        $siswaIdsOnPage = $siswas->pluck('id');

        $semuaNilaiBulanIni = Nilai::whereIn('siswa_id', $siswaIdsOnPage)
            ->where('mata_pelajaran_id', $mapel_id)
            ->where('guru_id', $guru_id)
            ->whereYear('tanggal', $selectedMonth->year)
            ->whereMonth('tanggal', $selectedMonth->month)
            ->get();

        $rataRataNilai = $semuaNilaiBulanIni->groupBy('siswa_id')->map(function ($nilaiSiswa) {
            $numericValues = $nilaiSiswa->pluck('nilai_tugas')->filter(fn($value) => is_numeric($value));
            return $numericValues->isNotEmpty() ? round($numericValues->avg(), 1) : '-';
        });

        $nilaiHarian = $semuaNilaiBulanIni->groupBy('siswa_id')
            ->map(function ($nilaiSiswa) {
                return $nilaiSiswa->keyBy(fn($item) => Carbon::parse($item->tanggal)->day);
            });
            
        return [$siswas, $nilaiHarian, $rataRataNilai, $daysInMonth, $selectedMonth];
    }
}
