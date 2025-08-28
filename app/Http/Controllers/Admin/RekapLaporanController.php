<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapLaporanExport;

class RekapLaporanController extends Controller
{
    public function index(Request $request)
    {
        // Data untuk filter dropdown
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $gurus = Guru::with('user')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();

        // Inisialisasi variabel kosong
        $dailyRecords = new LengthAwarePaginator([], 0, 10);
        $rekapNilaiGrafik = collect();
        $rekapPresensiGrafik = collect();

        // Hanya proses jika ada filter yang diterapkan (minimal bulan)
        if ($request->has('bulan') && $request->input('bulan') != '') {
            list($dailyRecords, $rekapNilaiGrafik, $rekapPresensiGrafik) = $this->fetchRekapData($request, true);
        }

        return view('admin.rekap.index', compact(
            'jurusans', 'kelas', 'gurus', 'mapels', 'dailyRecords',
            'rekapNilaiGrafik', 'rekapPresensiGrafik'
        ));
    }

    public function exportExcel(Request $request)
    {
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        $selectedMonth = Carbon::parse($request->input('bulan', now()->format('Y-m')))->locale('id');
        
        list($dailyRecords) = $this->fetchRekapData($request, false); // Ambil semua data tanpa pagination

        return Excel::download(new RekapLaporanExport($dailyRecords, $mapels, $selectedMonth), 'rekap-laporan-siswa-'.$selectedMonth->format('F-Y').'.xlsx');
    }

    private function fetchRekapData(Request $request, bool $paginate)
    {
        $bulan = $request->input('bulan');
        $jurusan_id = $request->input('jurusan_id');
        $kelas_id = $request->input('kelas_id');
        $guru_asuh_id = $request->input('guru_asuh_id');

        // Query dasar untuk siswa
        $siswaQuery = Siswa::query();
        
        if ($jurusan_id) $siswaQuery->whereHas('kelas', fn($q) => $q->where('jurusan_id', $jurusan_id));
        if ($kelas_id) $siswaQuery->where('kelas_id', $kelas_id);
        
        if ($guru_asuh_id) {
            $siswaQuery->whereHas('guruAsuh', function ($query) use ($guru_asuh_id) {
                $query->where('guru_id', $guru_asuh_id);
            });
        }

        $siswaIds = $siswaQuery->pluck('id');

        // Ambil semua data relevan
        $allPresensi = Presensi::with('siswa.user', 'siswa.kelas', 'siswa.guruAsuh.guru.user')->whereIn('siswa_id', $siswaIds)
            ->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))->get();

        $allNilai = Nilai::with('siswa.user', 'siswa.kelas', 'siswa.guruAsuh.guru.user', 'mataPelajaran')->whereIn('siswa_id', $siswaIds)
            ->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))
            ->where('jenis_nilai', 'Harian')->get();

        $tempData = [];
        foreach ($allPresensi as $p) {
            $key = $p->siswa_id . '-' . $p->tanggal;
            if (!isset($tempData[$key])) $tempData[$key] = ['siswa' => $p->siswa, 'tanggal' => $p->tanggal, 'presensi' => [], 'nilai' => []];
            $tempData[$key]['presensi'][$p->mata_pelajaran_id] = $p->status;
        }
        foreach ($allNilai as $n) {
            $key = $n->siswa_id . '-' . $n->tanggal;
            if (!isset($tempData[$key])) $tempData[$key] = ['siswa' => $n->siswa, 'tanggal' => $n->tanggal, 'presensi' => [], 'nilai' => []];
            $tempData[$key]['nilai'][$n->mata_pelajaran_id] = $n->nilai_tugas;
        }

        $sortedRecords = collect($tempData)->sortBy(fn($i) => $i['siswa']->user->name)->sortByDesc(fn($i) => $i['tanggal']);

        $dailyRecords = $sortedRecords;
        if ($paginate) {
            $perPage = $request->input('perPage', 10);
            $currentPage = Paginator::resolveCurrentPage('page');
            $currentPageItems = $sortedRecords->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $dailyRecords = new LengthAwarePaginator($currentPageItems, count($sortedRecords), $perPage, $currentPage, ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'page']);
            $dailyRecords->withQueryString();
        }
        
        // PERBAIKAN: Menggunakan metode koleksi yang lebih aman untuk menghitung data grafik
        $rekapNilaiGrafik = $allNilai
            ->groupBy('mataPelajaran.nama_mapel')
            ->map(function ($nilaiPerMapel) {
                $numericValues = $nilaiPerMapel->pluck('nilai_tugas')->filter(fn($value) => is_numeric($value));
                return $numericValues->isNotEmpty() ? round($numericValues->avg(), 1) : 0;
            });

        $rekapPresensiGrafik = $allPresensi->countBy('status');

        return [$dailyRecords, $rekapNilaiGrafik, $rekapPresensiGrafik];
    }
}
