<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruAsuh;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GuruAsuhController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);

        $assignmentsQuery = GuruAsuh::query()
            ->with(['guru.user', 'siswa.user', 'siswa.kelas'])
            ->join('siswas', 'guru_asuh.siswa_id', '=', 'siswas.id')
            ->join('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            ->join('users', 'siswas.user_id', '=', 'users.id')
            ->select('guru_asuh.*') // Hindari konflik nama kolom
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('users.name', 'asc');

        if ($search) {
            $assignmentsQuery->where(function ($query) use ($search) {
                $query->whereHas('siswa.user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('guru.user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $assignments = $assignmentsQuery->paginate($perPage)->withQueryString();

        // Data untuk modal
        $gurus = Guru::with('user')->get();
        $assignedSiswaIds = GuruAsuh::pluck('siswa_id');
        $siswas = Siswa::with('user', 'kelas')->whereNotIn('id', $assignedSiswaIds)->get()->sortBy('user.name')->groupBy('kelas.nama_kelas');

        return view('admin.guruasuh.index', compact('assignments', 'gurus', 'siswas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guru_id' => 'required|exists:gurus,id',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswas,id',
        ], [
            'siswa_ids.required' => 'Anda harus memilih minimal satu siswa.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $guru_id = $request->guru_id;
            foreach ($request->siswa_ids as $siswa_id) {
                if (!GuruAsuh::where('siswa_id', $siswa_id)->exists()) {
                    GuruAsuh::create([
                        'guru_id' => $guru_id,
                        'siswa_id' => $siswa_id,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Siswa asuh berhasil ditetapkan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(GuruAsuh $guruAsuh)
    {
        $guruAsuh->delete();
        return response()->json(['success' => true, 'message' => 'Penetapan Guru Asuh berhasil dihapus!']);
    }
}
