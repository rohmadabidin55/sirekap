<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InputNilaiController extends Controller
{
    public function index()
    {
        // Data untuk filter dropdown
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();

        return view('admin.inputnilai.index', compact('jurusans', 'kelas', 'mapels'));
    }

    public function getSiswa(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
        ]);

        $siswas = Siswa::where('kelas_id', $request->kelas_id)
            ->with('user')
            ->join('users', 'siswas.user_id', '=', 'users.id')
            ->select('siswas.*')
            ->orderBy('users.name', 'asc')
            ->get();

        $siswaIds = $siswas->pluck('id');

        $nilai = Nilai::whereIn('siswa_id', $siswaIds)
            ->whereDate('tanggal', $request->tanggal)
            ->get()->groupBy('siswa_id')->map(fn($item) => $item->keyBy('mata_pelajaran_id'));

        $kehadiran = Presensi::whereIn('siswa_id', $siswaIds)
            ->whereDate('tanggal', $request->tanggal)
            ->get()->groupBy('siswa_id')->map(fn($item) => $item->keyBy('mata_pelajaran_id'));
        
        $mapels = MataPelajaran::all();

        $dataSiswa = $siswas->map(function ($siswa) use ($nilai, $kehadiran, $mapels) {
            $siswaData = [
                'id' => $siswa->id,
                'user' => $siswa->user,
                'nilai' => [],
                'presensi' => [],
            ];

            foreach ($mapels as $mapel) {
                $siswaData['nilai'][$mapel->id] = $nilai->get($siswa->id, collect())->get($mapel->id)->nilai_tugas ?? null;
                $siswaData['presensi'][$mapel->id] = $kehadiran->get($siswa->id, collect())->get($mapel->id)->status ?? 'Hadir';
            }
            return $siswaData;
        });

        return response()->json($dataSiswa);
    }

    public function updateNilaiDanKehadiran(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'data' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->data as $siswaData) {
                // Proses presensi
                foreach ($siswaData['presensi'] as $mapel_id => $status) {
                    $penugasan = DB::table('guru_mata_pelajaran')->where('mata_pelajaran_id', $mapel_id)->where('kelas_id', $request->kelas_id)->first();
                    $guru_id = $penugasan ? $penugasan->guru_id : null;
                    if (!$guru_id) continue; // Lewati jika tidak ada guru yang ditugaskan

                    Presensi::updateOrCreate(
                        ['siswa_id' => $siswaData['id'], 'mata_pelajaran_id' => $mapel_id, 'tanggal' => $request->tanggal],
                        ['guru_id' => $guru_id, 'status' => $status]
                    );
                }

                // Proses nilai
                foreach ($siswaData['nilai'] as $mapel_id => $nilai_tugas) {
                    if (isset($nilai_tugas) && $nilai_tugas !== '') {
                        $penugasan = DB::table('guru_mata_pelajaran')->where('mata_pelajaran_id', $mapel_id)->where('kelas_id', $request->kelas_id)->first();
                        $guru_id = $penugasan ? $penugasan->guru_id : null;
                        if (!$guru_id) continue;

                        Nilai::updateOrCreate(
                            ['siswa_id' => $siswaData['id'], 'mata_pelajaran_id' => $mapel_id, 'tanggal' => $request->tanggal, 'jenis_nilai' => 'Harian'],
                            ['guru_id' => $guru_id, 'nilai_tugas' => $nilai_tugas]
                        );
                    }
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
