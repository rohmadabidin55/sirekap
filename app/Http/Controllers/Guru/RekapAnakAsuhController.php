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

class RekapAnakAsuhController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        if (!$guru) {
            abort(403, 'Akses Ditolak');
        }

        // Ambil ID siswa yang diasuh oleh guru ini
        $siswaAsuhIds = GuruAsuh::where('guru_id', $guru->id)->pluck('siswa_id');
        $siswaAsuh = Siswa::with('kelas.jurusan')->whereIn('id', $siswaAsuhIds)->get();

        // Siapkan data unik untuk dropdown filter dari siswa yang diasuh saja
        $jurusans = $siswaAsuh->pluck('kelas.jurusan')->unique('id')->sortBy('nama_jurusan');
        $kelas = $siswaAsuh->pluck('kelas')->unique('id')->sortBy('nama_kelas');
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        
        $isGuruAsuh = $siswaAsuhIds->isNotEmpty();

        // Inisialisasi variabel
        $dailyRecords = collect();
        $rekapNilaiGrafik = collect();
        $rekapPresensiGrafik = collect();

        // Hanya proses jika filter bulan sudah diisi
        if ($request->filled('bulan')) {
            $bulan = $request->input('bulan');
            $jurusan_id = $request->input('jurusan_id');
            $kelas_id = $request->input('kelas_id');
            $siswa_nama = $request->input('siswa_nama');

            // Filter siswa asuh berdasarkan input
            $filteredSiswaQuery = Siswa::whereIn('id', $siswaAsuhIds);
            if ($jurusan_id) {
                $filteredSiswaQuery->whereHas('kelas', fn($q) => $q->where('jurusan_id', $jurusan_id));
            }
            if ($kelas_id) {
                $filteredSiswaQuery->where('kelas_id', $kelas_id);
            }
            if ($siswa_nama) {
                $filteredSiswaQuery->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $siswa_nama . '%'));
            }
            $filteredSiswaIds = $filteredSiswaQuery->pluck('id');

            // Ambil semua data relevan
            $allNilai = Nilai::with('siswa.user', 'siswa.kelas')->whereIn('siswa_id', $filteredSiswaIds)
                ->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))
                ->where('jenis_nilai', 'Harian')->get();

            $allPresensi = Presensi::with('siswa.user', 'siswa.kelas')->whereIn('siswa_id', $filteredSiswaIds)
                ->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))
                ->get();
            
            // Gabungkan data
            $tempData = [];
            foreach ($allPresensi as $presensi) {
                $key = $presensi->siswa_id . '-' . $presensi->tanggal;
                if (!isset($tempData[$key])) {
                    $tempData[$key] = ['siswa' => $presensi->siswa, 'tanggal' => $presensi->tanggal, 'nilai' => [], 'presensi' => []];
                }
                $tempData[$key]['presensi'][$presensi->mata_pelajaran_id] = $presensi->status;
            }
            foreach ($allNilai as $nilai) {
                $key = $nilai->siswa_id . '-' . $nilai->tanggal;
                if (!isset($tempData[$key])) {
                    $tempData[$key] = ['siswa' => $nilai->siswa, 'tanggal' => $nilai->tanggal, 'nilai' => [], 'presensi' => []];
                }
                $tempData[$key]['nilai'][$nilai->mata_pelajaran_id] = $nilai->nilai_tugas;
            }

            // Urutkan
            $dailyRecords = collect($tempData)->sortBy(fn($item) => $item['siswa']->user->name)->sortByDesc(fn($item) => $item['tanggal']);

            // PERBAIKAN: Menggunakan DB Query Builder yang lebih andal untuk grafik
            $rekapNilaiGrafik = DB::table('nilais')
                ->join('mata_pelajarans', 'nilais.mata_pelajaran_id', '=', 'mata_pelajarans.id')
                ->whereIn('siswa_id', $filteredSiswaIds)
                ->whereYear('tanggal', date('Y', strtotime($bulan)))
                ->whereMonth('tanggal', date('m', strtotime($bulan)))
                ->select('mata_pelajarans.nama_mapel', DB::raw('AVG(nilai_tugas) as rata_rata'))
                ->groupBy('mata_pelajarans.nama_mapel')
                ->pluck('rata_rata', 'nama_mapel');

            $rekapPresensiGrafik = DB::table('presensis')
                ->whereIn('siswa_id', $filteredSiswaIds)
                ->whereYear('tanggal', date('Y', strtotime($bulan)))
                ->whereMonth('tanggal', date('m', strtotime($bulan)))
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
        }

        return view('guru.rekap-anak-asuh.index', compact(
            'guru', 'isGuruAsuh', 'jurusans', 'kelas', 'mapels', 'dailyRecords',
            'rekapNilaiGrafik', 'rekapPresensiGrafik'
        ));
    }
}
