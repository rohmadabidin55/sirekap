<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruMataPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Data untuk Kartu Statistik
        $jumlahGuru = Guru::count();
        $jumlahSiswa = Siswa::count();
        $jumlahKelas = Kelas::count();
        $jumlahMapel = MataPelajaran::count();

        // 2. Data untuk Grafik Kehadiran (Pie Chart) - Keseluruhan
        $rekapKehadiran = Presensi::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')->all();

        // 3. Data untuk Grafik Nilai (Bar Chart) - Keseluruhan
        $rekapNilai = Nilai::join('mata_pelajarans', 'nilais.mata_pelajaran_id', '=', 'mata_pelajarans.id')
            ->select('mata_pelajarans.nama_mapel', DB::raw('avg(nilais.nilai_tugas) as rata_rata'))
            ->whereNotNull('nilais.nilai_tugas')
            ->groupBy('mata_pelajarans.nama_mapel')
            ->pluck('rata_rata', 'nama_mapel')->all();

        // 4. Data Tabel: Guru yang belum input nilai PADA TANGGAL TERTENTU
        $tanggalFilter = $request->input('tanggal_filter', now()->format('Y-m-d'));
        
        // Ambil ID semua guru yang aktif mengajar (memiliki jadwal)
        $guruAktifIds = GuruMataPelajaran::select('guru_id')->distinct()->pluck('guru_id');

        // Ambil ID guru yang SUDAH input nilai pada tanggal yang difilter
        $guruSudahInputPadaTanggal = Nilai::whereDate('tanggal', $tanggalFilter)
            ->select('guru_id')->distinct()->pluck('guru_id');

        // Cari guru aktif yang ID-nya TIDAK ADA di daftar yang sudah input
        $guruBelumInputNilaiIds = $guruAktifIds->diff($guruSudahInputPadaTanggal);
        $guruBelumInputNilai = Guru::with('user')->whereIn('id', $guruBelumInputNilaiIds)->get();

        // 5. Data Tabel: Guru yang belum terjadwal sama sekali
        $guruSudahTerjadwal = GuruMataPelajaran::select('guru_id')->distinct()->pluck('guru_id');
        $guruBelumTerjadwal = Guru::with('user')->whereNotIn('id', $guruSudahTerjadwal)->get();

        return view('admin.dashboard.index', compact(
            'jumlahGuru',
            'jumlahSiswa',
            'jumlahKelas',
            'jumlahMapel',
            'rekapKehadiran',
            'rekapNilai',
            'guruBelumInputNilai',
            'guruBelumTerjadwal',
            'tanggalFilter' // Kirim tanggal ke view
        ));
    }
}
