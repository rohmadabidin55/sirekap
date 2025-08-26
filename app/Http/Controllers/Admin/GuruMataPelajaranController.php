<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruMataPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GuruMataPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);

        $assignmentsQuery = GuruMataPelajaran::query()
            ->with(['guru.user', 'mataPelajaran', 'kelas'])
            ->join('kelas', 'guru_mata_pelajaran.kelas_id', '=', 'kelas.id')
            ->join('mata_pelajarans', 'guru_mata_pelajaran.mata_pelajaran_id', '=', 'mata_pelajarans.id')
            ->select('guru_mata_pelajaran.*') // Hindari konflik nama kolom
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('mata_pelajarans.nama_mapel', 'asc');

        if ($search) {
            $assignmentsQuery->where(function ($query) use ($search) {
                $query->whereHas('guru.user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('mataPelajaran', function ($q) use ($search) {
                    $q->where('nama_mapel', 'like', '%' . $search . '%');
                })->orWhereHas('kelas', function ($q) use ($search) {
                    $q->where('nama_kelas', 'like', '%' . $search . '%');
                });
            });
        }

        $assignments = $assignmentsQuery->paginate($perPage)->withQueryString();

        // Data untuk modal
        $gurus = Guru::with('user')->get();
        $mataPelajarans = MataPelajaran::orderBy('nama_mapel')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('admin.gurumatapelajaran.index', compact('assignments', 'gurus', 'mataPelajarans', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guru_id' => 'required|exists:gurus,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas_id' => [
                'required',
                'exists:kelas,id',
                Rule::unique('guru_mata_pelajaran')->where(function ($query) use ($request) {
                    return $query->where('guru_id', $request->guru_id)
                                 ->where('mata_pelajaran_id', $request->mata_pelajaran_id);
                }),
            ],
        ], [
            'kelas_id.unique' => 'Guru ini sudah ditugaskan untuk mengajar mata pelajaran ini di kelas yang sama.'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        GuruMataPelajaran::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Penugasan Guru Berhasil Disimpan!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GuruMataPelajaran $guruMataPelajaran)
    {
        $guruMataPelajaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penugasan Guru Berhasil Dihapus!',
        ]);
    }
}
