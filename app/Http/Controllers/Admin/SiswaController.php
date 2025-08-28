<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Exports\SiswaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\SiswaTemplateExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Imports\SiswaImport; // Tambahkan ini
use Maatwebsite\Excel\Facades\Excel; // Tambahkan ini

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $kelas_filter = $request->query('kelas_filter');
        $perPage = $request->query('perPage', 10); // Default 10 data per halaman

        $siswasQuery = Siswa::with(['user', 'kelas'])
            ->join('users', 'siswas.user_id', '=', 'users.id')
            ->select('siswas.*')
            ->orderBy('users.name', 'asc');

        // Filter berdasarkan pencarian nama
        if ($search) {
            $siswasQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kelas
        if ($kelas_filter) {
            $siswasQuery->where('kelas_id', $kelas_filter);
        }

        $siswas = $siswasQuery->paginate($perPage)->withQueryString();
        
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('admin.siswa.index', compact('siswas', 'kelas'));
    }

        public function exportExcel()
    {
        return Excel::download(new SiswaExport, 'data-siswa-'.date('Y-m-d').'.xlsx');
    }

    /**
     * Fungsi baru untuk mengunduh template Excel.
     */
    public function exportTemplate()
    {
        return Excel::download(new SiswaTemplateExport, 'template_import_siswa.xlsx');
    }

    /**
     * Fungsi baru untuk menangani import file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file'));
            return response()->json(['success' => true, 'message' => 'Data siswa berhasil diimpor!']);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errorMessages = [];
             foreach ($failures as $failure) {
                 $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
             }
             return response()->json(['success' => false, 'message' => 'Terjadi kesalahan validasi.', 'errors' => $errorMessages], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nis' => 'required|string|max:50|unique:siswas',
            'nisn' => 'nullable|string|max:50|unique:siswas',
            'kelas_id' => 'required|exists:kelas,id',
            'alamat' => 'nullable|string',
            'no_telepon_orang_tua' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('photos/siswa', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa',
                'photo' => $photoPath,
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $request->kelas_id,
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'alamat' => $request->alamat,
                'no_telepon_orang_tua' => $request->no_telepon_orang_tua,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data Siswa Berhasil Disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $siswa->user_id,
            'nis' => 'required|string|max:50|unique:siswas,nis,' . $siswa->id,
            'nisn' => 'nullable|string|max:50|unique:siswas,nisn,' . $siswa->id,
            'kelas_id' => 'required|exists:kelas,id',
            'password' => 'nullable|string|min:8',
            'alamat' => 'nullable|string',
            'no_telepon_orang_tua' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        DB::beginTransaction();
        try {
            $user = $siswa->user;
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
                $userData['photo'] = $request->file('photo')->store('photos/siswa', 'public');
            }

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            $siswa->update($request->except(['name', 'email', 'password', 'photo']));

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data Siswa Berhasil Diperbarui!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        DB::beginTransaction();
        try {
            if ($siswa->user && $siswa->user->photo) {
                Storage::disk('public')->delete($siswa->user->photo);
            }
            
            $siswa->user()->delete();
            $siswa->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data Siswa Berhasil Dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
