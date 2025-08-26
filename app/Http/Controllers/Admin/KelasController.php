<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);

        $kelasQuery = Kelas::with('jurusan')->latest();

        // Filter berdasarkan pencarian nama kelas atau nama jurusan
        if ($search) {
            $kelasQuery->where('nama_kelas', 'like', '%' . $search . '%')
                ->orWhereHas('jurusan', function ($query) use ($search) {
                    $query->where('nama_jurusan', 'like', '%' . $search . '%');
                });
        }

        $kelasList = $kelasQuery->paginate($perPage)->withQueryString();
        
        // Ambil data jurusan untuk dropdown di form
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        return view('admin.kelas.index', compact('kelasList', 'jurusans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255|unique:kelas',
            'tingkat' => ['required', Rule::in(['10', '11', '12'])],
            'jurusan_id' => 'required|exists:jurusans,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kelas = Kelas::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Kelas Berhasil Disimpan!',
            'data'    => $kelas->load('jurusan')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela) // Laravel uses singular form 'kela'
    {
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas,' . $kela->id,
            'tingkat' => ['required', Rule::in(['10', '11', '12'])],
            'jurusan_id' => 'required|exists:jurusans,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kela->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Kelas Berhasil Diperbarui!',
            'data'    => $kela->load('jurusan')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Kelas Berhasil Dihapus!',
        ]);
    }
}
