<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);

        $gurusQuery = Guru::with('user')->latest();

        // Filter berdasarkan pencarian nama
        if ($search) {
            $gurusQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        $gurus = $gurusQuery->paginate($perPage)->withQueryString();

        return view('admin.guru.index', compact('gurus'));
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
            'nip' => 'nullable|string|max:50|unique:gurus,nip', // NIP sekarang opsional
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('photos/guru', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'guru',
                'photo' => $photoPath,
            ]);

            Guru::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'alamat' => $request->alamat,
                'no_telepon' => $request->no_telepon,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data Guru Berhasil Disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $guru->user_id,
            'nip' => 'nullable|string|max:50|unique:gurus,nip,' . $guru->id, // NIP sekarang opsional
            'password' => 'nullable|string|min:8',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        DB::beginTransaction();
        try {
            $user = $guru->user;
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
                $userData['photo'] = $request->file('photo')->store('photos/guru', 'public');
            }

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            $guru->update($request->except(['name', 'email', 'password', 'photo']));

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data Guru Berhasil Diperbarui!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guru $guru)
    {
        DB::beginTransaction();
        try {
            if ($guru->user && $guru->user->photo) {
                Storage::disk('public')->delete($guru->user->photo);
            }
            
            $guru->user()->delete();
            $guru->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data Guru Berhasil Dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
