@extends('layouts.guru')

@section('title', 'Edit Profil')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6 md:p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Profil</h2>

    @if (session('status'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-lg" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Foto Profil -->
            <div class="md:col-span-1 flex flex-col items-center">
                <img id="photoPreview" class="h-40 w-40 rounded-full object-cover ring-4 ring-indigo-100" src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://placehold.co/200x200/E2E8F0/4A5568?text=G' }}" alt="Foto Profil">
                <label for="photo" class="mt-4 cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Ganti Foto
                </label>
                <input type="file" name="photo" id="photo" class="hidden" onchange="document.getElementById('photoPreview').src = window.URL.createObjectURL(this.files[0])">
                @error('photo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Kolom Kanan: Data Profil -->
            <div class="md:col-span-2 space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                    <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $user->guru->no_telepon) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">{{ old('alamat', $user->guru->alamat) }}</textarea>
                </div>

                <hr class="my-6">

                <h3 class="text-lg font-medium text-gray-800">Ubah Password</h3>
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    @error('current_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" name="new_password" id="new_password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    @error('new_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-8">
            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
