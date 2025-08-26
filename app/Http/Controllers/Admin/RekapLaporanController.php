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
        $dailyRecords = collect();
        $rekapNilaiGrafik = collect();
        $rekapPresensiGrafik = collect();

        // Hanya proses jika ada filter yang diterapkan (minimal bulan)
        if ($request->has('bulan') && $request->input('bulan') != '') {
            $bulan = $request->input('bulan');
            $jurusan_id = $request->input('jurusan_id');
            $kelas_id = $request->input('kelas_id');
            $guru_asuh_id = $request->input('guru_asuh_id');

            // Query dasar untuk siswa
            $siswaQuery = Siswa::query();
            if ($jurusan_id) $siswaQuery->whereHas('kelas', fn($q) => $q->where('jurusan_id', $jurusan_id));
            if ($kelas_id) $siswaQuery->where('kelas_id', $kelas_id);
            if ($guru_asuh_id) $siswaQuery->whereHas('guruAsuh', fn($q) => $q->where('guru_id', $guru_asuh_id));
            $siswaIds = $siswaQuery->pluck('id');

            // Ambil semua data relevan
            $allPresensi = Presensi::with('siswa.user', 'siswa.kelas', 'siswa.guruAsuh.guru.user')
                ->whereIn('siswa_id', $siswaIds)
                ->whereYear('tanggal', date('Y', strtotime($bulan)))
                ->whereMonth('tanggal', date('m', strtotime($bulan)))
                ->get();

            $allNilai = Nilai::with('siswa.user', 'siswa.kelas', 'siswa.guruAsuh.guru.user')
                ->whereIn('siswa_id', $siswaIds)
                ->whereYear('tanggal', date('Y', strtotime($bulan)))
                ->whereMonth('tanggal', date('m', strtotime($bulan)))
                ->where('jenis_nilai', 'Harian')
                ->get();

            // Gabungkan data presensi dan nilai ke dalam satu struktur harian
            $tempData = [];
            foreach ($allPresensi as $presensi) {
                $key = $presensi->siswa_id . '-' . $presensi->tanggal;
                if (!isset($tempData[$key])) {
                    $tempData[$key] = [
                        'siswa' => $presensi->siswa,
                        'tanggal' => $presensi->tanggal,
                        'presensi' => [],
                        'nilai' => [],
                    ];
                }
                $tempData[$key]['presensi'][$presensi->mata_pelajaran_id] = $presensi->status;
            }
            foreach ($allNilai as $nilai) {
                $key = $nilai->siswa_id . '-' . $nilai->tanggal;
                if (!isset($tempData[$key])) {
                    $tempData[$key] = [
                        'siswa' => $nilai->siswa,
                        'tanggal' => $nilai->tanggal,
                        'presensi' => [],
                        'nilai' => [],
                    ];
                }
                $tempData[$key]['nilai'][$nilai->mata_pelajaran_id] = $nilai->nilai_tugas;
            }

            // Urutkan berdasarkan tanggal (desc), lalu nama siswa (asc)
            $sortedRecords = collect($tempData)
                ->sortBy(fn($item) => $item['siswa']->user->name)
                ->sortByDesc(fn($item) => $item['tanggal']);

            // PERBAIKAN: Terapkan pagination secara manual
            $perPage = $request->input('perPage', 10);
            $currentPage = Paginator::resolveCurrentPage('page');
            $currentPageItems = $sortedRecords->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $dailyRecords = new LengthAwarePaginator($currentPageItems, count($sortedRecords), $perPage, $currentPage, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $dailyRecords->withQueryString();
                
            // Data untuk Grafik (tetap ringkasan bulanan)
            $rekapNilaiGrafik = DB::table('nilais')->join('mata_pelajarans', 'nilais.mata_pelajaran_id', '=', 'mata_pelajarans.id')
                ->whereIn('siswa_id', $siswaIds)->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))
                ->select('mata_pelajarans.nama_mapel', DB::raw('AVG(nilai_tugas) as rata_rata'))->groupBy('mata_pelajarans.nama_mapel')
                ->pluck('rata_rata', 'nama_mapel');

            $rekapPresensiGrafik = DB::table('presensis')->whereIn('siswa_id', $siswaIds)
                ->whereYear('tanggal', date('Y', strtotime($bulan)))->whereMonth('tanggal', date('m', strtotime($bulan)))
                ->select('status', DB::raw('count(*) as total'))->groupBy('status')
                ->pluck('total', 'status');
        }

        return view('admin.rekap.index', compact(
            'jurusans', 'kelas', 'gurus', 'mapels', 'dailyRecords',
            'rekapNilaiGrafik', 'rekapPresensiGrafik'
        ));
    }
}
