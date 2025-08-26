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

class RekapPerGuruController extends Controller
{
    public function index(Request $request)
    {
        // Data untuk filter dropdown
        $gurus = Guru::with('user')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();

        // Inisialisasi variabel
        $siswas = collect();
        $nilaiHarian = collect();
        $rataRataNilai = collect();
        $daysInMonth = 0;
        $selectedMonth = '';

        // Hanya proses jika semua filter yang diperlukan sudah diisi
        if ($request->filled(['bulan', 'guru_id', 'mapel_id'])) {
            $bulan = $request->input('bulan');
            $guru_id = $request->input('guru_id');
            $jurusan_id = $request->input('jurusan_id');
            $mapel_id = $request->input('mapel_id');
            $perPage = $request->input('perPage', 10);
            
            $selectedMonth = Carbon::parse($bulan)->locale('id');
            $daysInMonth = $selectedMonth->daysInMonth;

            // Query dasar untuk siswa (yang akan dipaginasi)
            $siswaQuery = Siswa::with(['user', 'guruAsuh.guru.user'])
                ->join('users', 'siswas.user_id', '=', 'users.id')
                ->select('siswas.*')
                ->orderBy('users.name', 'asc');

            $siswaQuery->whereHas('kelas', function ($query) use ($jurusan_id, $guru_id, $mapel_id) {
                if ($jurusan_id) {
                    $query->where('jurusan_id', $jurusan_id);
                }
                $query->whereHas('guruMataPelajaranAssignments', function ($subQuery) use ($guru_id, $mapel_id) {
                    $subQuery->where('guru_id', $guru_id)
                             ->where('mata_pelajaran_id', $mapel_id);
                });
            });
            
            // Terapkan pagination pada query siswa
            $siswas = $siswaQuery->paginate($perPage)->withQueryString();
            $siswaIdsOnPage = $siswas->pluck('id');

            // Ambil semua nilai harian HANYA untuk siswa di halaman saat ini
            $semuaNilaiBulanIni = Nilai::whereIn('siswa_id', $siswaIdsOnPage)
                ->where('mata_pelajaran_id', $mapel_id)
                ->where('guru_id', $guru_id)
                ->whereYear('tanggal', $selectedMonth->year)
                ->whereMonth('tanggal', $selectedMonth->month)
                ->get();

            // Hitung rata-rata nilai untuk setiap siswa
            $rataRataNilai = $semuaNilaiBulanIni->groupBy('siswa_id')->map(function ($nilaiSiswa) {
                return round($nilaiSiswa->avg('nilai_tugas'), 1);
            });

            // Kelompokkan nilai harian untuk ditampilkan di tabel
            $nilaiHarian = $semuaNilaiBulanIni->groupBy('siswa_id')
                ->map(function ($nilaiSiswa) {
                    return $nilaiSiswa->keyBy(fn($item) => Carbon::parse($item->tanggal)->day);
                });
        }

        return view('admin.rekappergu.index', compact(
            'gurus', 'jurusans', 'mapels', 'siswas', 'nilaiHarian', 'rataRataNilai', 'daysInMonth', 'selectedMonth'
        ));
    }
}
