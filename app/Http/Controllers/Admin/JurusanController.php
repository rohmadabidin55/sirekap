<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JurusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jurusans = Jurusan::latest()->get();
            return response()->json(['data' => $jurusans]);
        }
        return view('admin.jurusan.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_jurusan' => 'required|string|max:255|unique:jurusans,nama_jurusan',
            'singkatan' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $jurusan = Jurusan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Jurusan Berhasil Disimpan!',
            'data'    => $jurusan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurusan $jurusan)
    {
        $validator = Validator::make($request->all(), [
            'nama_jurusan' => 'required|string|max:255|unique:jurusans,nama_jurusan,' . $jurusan->id,
            'singkatan' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $jurusan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Jurusan Berhasil Diperbarui!',
            'data'    => $jurusan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Jurusan Berhasil Dihapus!',
        ]);
    }
}
