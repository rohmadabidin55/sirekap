<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SekolahController extends Controller
{
    public function index()
    {
        // Ambil data pertama, atau buat baru jika belum ada
        $sekolah = Sekolah::firstOrCreate(['id' => 1]);
        return view('admin.sekolah.index', compact('sekolah'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|string|max:20',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512',
        ]);

        $sekolah = Sekolah::find(1);
        $data = $request->except(['logo', 'favicon']);

        if ($request->hasFile('logo')) {
            if ($sekolah->logo) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($sekolah->favicon) {
                Storage::disk('public')->delete($sekolah->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('logos', 'public');
        }

        $sekolah->update($data);

        return back()->with('status', 'Data sekolah berhasil diperbarui!');
    }
}
