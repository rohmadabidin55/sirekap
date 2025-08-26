<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruAsuh;
use App\Models\GuruMataPelajaran;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard guru.
     */
    public function index()
    {
        $guru = Auth::user()->guru;
        if (!$guru) {
            abort(403, 'Akses Ditolak');
        }

        $penugasan = GuruMataPelajaran::where('guru_id', $guru->id)
            ->with(['mataPelajaran', 'kelas'])
            ->get();

        $mapelDanKelas = $penugasan->groupBy('mata_pelajaran_id')->map(function ($item) {
            return [
                'mata_pelajaran' => $item->first()->mataPelajaran,
                'kelas' => $item->pluck('kelas')->unique('id')->sortBy('nama_kelas'),
            ];
        });

        $isGuruAsuh = GuruAsuh::where('guru_id', $guru->id)->exists();

        return view('guru.dashboard.index', compact('guru', 'mapelDanKelas', 'isGuruAsuh'));
    }

    /**
     * Mengambil data siswa dan nilai untuk kelas dan mapel tertentu (AJAX).
     */
    public function getSiswa(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajarans,id',
            'tanggal' => 'required|date',
        ]);

        $siswas = Siswa::where('siswas.kelas_id', $request->kelas_id)
            ->join('users', 'siswas.user_id', '=', 'users.id')
            ->select('siswas.*')
            ->with('user')
            ->orderBy('users.name', 'asc')
            ->get();

        $nilai = Nilai::where('mata_pelajaran_id', $request->mapel_id)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->whereDate('tanggal', $request->tanggal)
            ->where('jenis_nilai', 'Harian')
            ->pluck('nilai_tugas', 'siswa_id');

        $kehadiran = Presensi::where('mata_pelajaran_id', $request->mapel_id)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->whereDate('tanggal', $request->tanggal)
            ->pluck('status', 'siswa_id');

        $dataSiswa = $siswas->map(function ($siswa) use ($nilai, $kehadiran) {
            $siswa->nilai_harian = $nilai[$siswa->id] ?? null;
            $siswa->kehadiran = $kehadiran[$siswa->id] ?? 'Hadir';
            return $siswa;
        });

        return response()->json($dataSiswa);
    }

    /**
     * Menyimpan atau memperbarui nilai dan kehadiran siswa secara massal.
     */
    public function updateNilaiDanKehadiran(Request $request)
    {
        $request->validate([
            'mapel_id' => 'required|exists:mata_pelajarans,id',
            'tanggal' => 'required|date',
            'data' => 'required|array',
            'data.*.id' => 'required|exists:siswas,id',
            'data.*.nilai_harian' => 'nullable|numeric|min:0|max:100',
            'data.*.kehadiran' => 'required|in:Hadir,Sakit,Izin,Alpa,PMS', // PERBAIKAN DI SINI
        ]);

        $guru_id = Auth::user()->guru->id;

        DB::beginTransaction();
        try {
            foreach ($request->data as $item) {
                Presensi::updateOrCreate(
                    [
                        'siswa_id' => $item['id'],
                        'mata_pelajaran_id' => $request->mapel_id,
                        'tanggal' => $request->tanggal,
                    ],
                    [
                        'guru_id' => $guru_id,
                        'status' => $item['kehadiran'],
                    ]
                );

                if (isset($item['nilai_harian']) && $item['nilai_harian'] !== '') {
                    Nilai::updateOrCreate(
                        [
                            'siswa_id' => $item['id'],
                            'mata_pelajaran_id' => $request->mapel_id,
                            'tanggal' => $request->tanggal,
                            'jenis_nilai' => 'Harian',
                        ],
                        [
                            'guru_id' => $guru_id,
                            'nilai_tugas' => $item['nilai_harian'],
                        ]
                    );
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Perubahan berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
