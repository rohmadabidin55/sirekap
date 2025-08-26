<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruAsuh;
use App\Models\GuruMataPelajaran;
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

class RekapMapelController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        if (!$guru) {
            abort(403, 'Akses Ditolak');
        }

        // Ambil data penugasan guru untuk filter
        $penugasan = GuruMataPelajaran::where('guru_id', $guru->id)
            ->with(['mataPelajaran', 'kelas.jurusan'])
            ->get();

        // Siapkan data unik untuk dropdown filter
        $mapels = $penugasan->pluck('mataPelajaran')->unique('id')->sortBy('nama_mapel');
        $kelas = $penugasan->pluck('kelas')->unique('id')->sortBy('nama_kelas');
        $jurusans = $penugasan->pluck('kelas.jurusan')->unique('id')->sortBy('nama_jurusan');
        
        $isGuruAsuh = GuruAsuh::where('guru_id', $guru->id)->exists();

        // Inisialisasi variabel
        $siswas = collect();
        $nilaiHarian = collect();
        $rataRataNilai = collect();
        $daysInMonth = 0;
        $selectedMonth = '';
        $rekapNilaiGrafik = collect();
        $rekapPresensiGrafik = collect();

        // Hanya proses jika filter sudah diisi
        if ($request->filled(['bulan', 'mapel_id'])) {
            $bulan = $request->input('bulan');
            $mapel_id = $request->input('mapel_id');
            $jurusan_id = $request->input('jurusan_id');
            $kelas_id = $request->input('kelas_id');
            $siswa_nama = $request->input('siswa_nama');
            
            $selectedMonth = Carbon::parse($bulan)->locale('id');
            $daysInMonth = $selectedMonth->daysInMonth;

            // Query siswa yang diajar oleh guru ini di mapel ini
            $kelasIdsYangDiajar = $penugasan->where('mata_pelajaran_id', $mapel_id)->pluck('kelas_id');
            
            $siswaQuery = Siswa::with(['user', 'kelas'])
                ->whereIn('kelas_id', $kelasIdsYangDiajar);

            if ($jurusan_id) {
                $siswaQuery->whereHas('kelas', fn($q) => $q->where('jurusan_id', $jurusan_id));
            }
            if ($kelas_id) {
                $siswaQuery->where('kelas_id', $kelas_id);
            }
            if ($siswa_nama) {
                $siswaQuery->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $siswa_nama . '%'));
            }
            
            $siswas = $siswaQuery->get()->sortBy('user.name');
            $siswaIds = $siswas->pluck('id');

            // Ambil nilai harian
            $semuaNilaiBulanIni = Nilai::whereIn('siswa_id', $siswaIds)
                ->where('mata_pelajaran_id', $mapel_id)
                ->where('guru_id', $guru->id)
                ->whereYear('tanggal', $selectedMonth->year)
                ->whereMonth('tanggal', $selectedMonth->month)
                ->get();

            $rataRataNilai = $semuaNilaiBulanIni->groupBy('siswa_id')->map(fn($n) => round($n->avg('nilai_tugas'), 1));
            $nilaiHarian = $semuaNilaiBulanIni->groupBy('siswa_id')->map(fn($n) => $n->keyBy(fn($item) => Carbon::parse($item->tanggal)->day));

            // Data untuk Grafik
            $rekapNilaiGrafik = $semuaNilaiBulanIni->groupBy(fn($item) => Carbon::parse($item->tanggal)->format('d-m-Y'))
                ->map(fn($item) => round($item->avg('nilai_tugas'), 1));

            $rekapPresensiGrafik = Presensi::whereIn('siswa_id', $siswaIds)
                ->where('mata_pelajaran_id', $mapel_id)
                ->whereYear('tanggal', $selectedMonth->year)
                ->whereMonth('tanggal', $selectedMonth->month)
                ->select('status', DB::raw('count(*) as total'))->groupBy('status')
                ->pluck('total', 'status');
        }

        return view('guru.rekap-mapel.index', compact(
            'guru', 'isGuruAsuh', 'mapels', 'kelas', 'jurusans', 
            'siswas', 'nilaiHarian', 'rataRataNilai', 'daysInMonth', 'selectedMonth',
            'rekapNilaiGrafik', 'rekapPresensiGrafik'
        ));
    }
}
