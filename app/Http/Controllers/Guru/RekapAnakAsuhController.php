<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruAsuh;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan ini

class RekapAnakAsuhController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        if (!$guru) {
            abort(403, 'Akses Ditolak');
        }

        $siswaAsuhIds = GuruAsuh::where('guru_id', $guru->id)->pluck('siswa_id');
        $siswaAsuh = Siswa::with('kelas.jurusan')->whereIn('id', $siswaAsuhIds)->get();

        $jurusans = $siswaAsuh->pluck('kelas.jurusan')->unique('id')->sortBy('nama_jurusan');
        $kelas = $siswaAsuh->pluck('kelas')->unique('id')->sortBy('nama_kelas');
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        
        $isGuruAsuh = $siswaAsuhIds->isNotEmpty();

        $dailyRecords = collect();
        $rekapNilaiGrafik = collect();
        $rekapPresensiGrafik = collect();

        if ($request->filled('bulan')) {
            list($dailyRecords, $rekapNilaiGrafik, $rekapPresensiGrafik) = $this->fetchRekapData($request, $siswaAsuhIds);
        }

        return view('guru.rekap-anak-asuh.index', compact(
            'guru', 'isGuruAsuh', 'jurusans', 'kelas', 'mapels', 'dailyRecords',
            'rekapNilaiGrafik', 'rekapPresensiGrafik'
        ));
    }

    public function exportPdf(Request $request)
    {
        $guru = Auth::user()->guru;
        $siswaAsuhIds = GuruAsuh::where('guru_id', $guru->id)->pluck('siswa_id');
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        $selectedMonth = Carbon::parse($request->input('bulan', now()->format('Y-m')))->locale('id');

        list($dailyRecords) = $this->fetchRekapData($request, $siswaAsuhIds);

        $pdf = Pdf::loadView('guru.rekap-anak-asuh.pdf', compact('dailyRecords', 'mapels', 'guru', 'selectedMonth'));
        return $pdf->stream('rekap-nilai-anak-asuh-'.$selectedMonth->format('F-Y').'.pdf');
    }

    private function fetchRekapData(Request $request, $siswaAsuhIds)
    {
        $bulan = $request->input('bulan');
        $jurusan_id = $request->input('jurusan_id');
        $kelas_id = $request->input('kelas_id');
        $siswa_nama = $request->input('siswa_nama');

        $filteredSiswaQuery = Siswa::whereIn('id', $siswaAsuhIds);
        if ($jurusan_id) $filteredSiswaQuery->whereHas('kelas', fn($q) => $q->where('jurusan_id', $jurusan_id));
        if ($kelas_id) $filteredSiswaQuery->where('kelas_id', $kelas_id);
        if ($siswa_nama) $filteredSiswaQuery->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $siswa_nama . '%'));
        $filteredSiswaIds = $filteredSiswaQuery->pluck('id');

        $allNilai = Nilai::with('siswa.user', 'siswa.kelas')->whereIn('siswa_id', $filteredSiswaIds)
            ->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))
            ->where('jenis_nilai', 'Harian')->get();

        $allPresensi = Presensi::with('siswa.user', 'siswa.kelas')->whereIn('siswa_id', $filteredSiswaIds)
            ->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))
            ->get();
        
        $tempData = [];
        foreach ($allPresensi as $presensi) {
            $key = $presensi->siswa_id . '-' . $presensi->tanggal;
            if (!isset($tempData[$key])) $tempData[$key] = ['siswa' => $presensi->siswa, 'tanggal' => $presensi->tanggal, 'nilai' => [], 'presensi' => []];
            $tempData[$key]['presensi'][$presensi->mata_pelajaran_id] = $presensi->status;
        }
        foreach ($allNilai as $nilai) {
            $key = $nilai->siswa_id . '-' . $nilai->tanggal;
            if (!isset($tempData[$key])) $tempData[$key] = ['siswa' => $nilai->siswa, 'tanggal' => $nilai->tanggal, 'nilai' => [], 'presensi' => []];
            $tempData[$key]['nilai'][$nilai->mata_pelajaran_id] = $nilai->nilai_tugas;
        }

        $dailyRecords = collect($tempData)->sortBy(fn($item) => $item['siswa']->user->name)->sortByDesc(fn($item) => $item['tanggal']);

        $rekapNilaiGrafik = DB::table('nilais')->join('mata_pelajarans', 'nilais.mata_pelajaran_id', '=', 'mata_pelajarans.id')
            ->whereIn('siswa_id', $filteredSiswaIds)->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))
            ->select('mata_pelajarans.nama_mapel', DB::raw('AVG(nilai_tugas) as rata_rata'))->groupBy('mata_pelajarans.nama_mapel')
            ->pluck('rata_rata', 'nama_mapel');

        $rekapPresensiGrafik = DB::table('presensis')->whereIn('siswa_id', $filteredSiswaIds)
            ->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))
            ->select('status', DB::raw('count(*) as total'))->groupBy('status')
            ->pluck('total', 'status');
        
        return [$dailyRecords, $rekapNilaiGrafik, $rekapPresensiGrafik];
    }
}
