<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MataPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);

        $mapelsQuery = MataPelajaran::query()->orderBy('nama_mapel', 'asc');

        // Filter berdasarkan pencarian nama atau kode
        if ($search) {
            $mapelsQuery->where(function ($query) use ($search) {
                $query->where('nama_mapel', 'like', '%' . $search . '%')
                      ->orWhere('kode_mapel', 'like', '%' . $search . '%');
            });
        }

        $mataPelajarans = $mapelsQuery->paginate($perPage)->withQueryString();

        return view('admin.matapelajaran.index', compact('mataPelajarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajarans',
            'nama_mapel' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mataPelajaran = MataPelajaran::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Mata Pelajaran Berhasil Disimpan!',
            'data'    => $mataPelajaran
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validator = Validator::make($request->all(), [
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajarans,kode_mapel,' . $mataPelajaran->id,
            'nama_mapel' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mataPelajaran->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Mata Pelajaran Berhasil Diperbarui!',
            'data'    => $mataPelajaran
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Mata Pelajaran Berhasil Dihapus!',
        ]);
    }
}
