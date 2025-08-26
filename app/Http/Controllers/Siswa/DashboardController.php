<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa->load('kelas.jurusan');
        if (!$siswa) {
            abort(403, 'Akses Ditolak');
        }

        // Ambil semua mata pelajaran untuk header tabel
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();

        // Inisialisasi variabel
        $dailyRecords = collect();
        $rekapNilaiGrafik = collect();
        $rekapPresensiGrafik = collect();

        // Ambil filter bulan, default ke bulan ini
        $bulan = $request->input('bulan', now()->format('Y-m'));

        // Ambil semua data nilai dan presensi untuk siswa ini pada bulan yang dipilih
        $allNilai = Nilai::where('siswa_id', $siswa->id)
            ->whereYear('tanggal', date('Y', strtotime($bulan)))
            ->whereMonth('tanggal', date('m', strtotime($bulan)))
            ->where('jenis_nilai', 'Harian')
            ->get();

        $allPresensi = Presensi::where('siswa_id', $siswa->id)
            ->whereYear('tanggal', date('Y', strtotime($bulan)))
            ->whereMonth('tanggal', date('m', strtotime($bulan)))
            ->get();
        
        // Gabungkan data ke dalam satu struktur harian
        $tempData = [];
        foreach ($allPresensi as $presensi) {
            $key = $presensi->tanggal;
            if (!isset($tempData[$key])) {
                $tempData[$key] = ['tanggal' => $presensi->tanggal, 'nilai' => [], 'presensi' => []];
            }
            $tempData[$key]['presensi'][$presensi->mata_pelajaran_id] = $presensi->status;
        }
        foreach ($allNilai as $nilai) {
            $key = $nilai->tanggal;
            if (!isset($tempData[$key])) {
                $tempData[$key] = ['tanggal' => $nilai->tanggal, 'nilai' => [], 'presensi' => []];
            }
            $tempData[$key]['nilai'][$nilai->mata_pelajaran_id] = $nilai->nilai_tugas;
        }

        // PERBAIKAN: Urutkan berdasarkan tanggal (descending)
        $dailyRecords = collect($tempData)->sortByDesc('tanggal');

        // Siapkan data untuk grafik
        $rekapNilaiGrafik = $allNilai->load('mataPelajaran')
            ->groupBy('mataPelajaran.nama_mapel')
            ->map(fn($item) => round($item->avg('nilai_tugas'), 1));

        $rekapPresensiGrafik = $allPresensi->countBy('status');


        return view('siswa.dashboard.index', compact(
            'siswa', 'mapels', 'dailyRecords', 'rekapNilaiGrafik', 'rekapPresensiGrafik'
        ));
    }
}
