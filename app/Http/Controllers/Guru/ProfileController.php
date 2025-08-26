<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Mengambil data profil guru saat ini untuk modal (AJAX).
     */
    public function show()
    {
        $user = Auth::user()->load('guru');
        return response()->json($user);
    }

    /**
     * Memperbarui data profil dari modal (AJAX).
     * Menggunakan POST karena FormData dengan file upload.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $guru = $user->guru;

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update data di tabel User
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('photos/guru', 'public');
        }

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        // Update data di tabel Guru
        $guru->update([
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'user' => $user->fresh()->load('guru') // Kirim data user terbaru
        ]);
    }
}
