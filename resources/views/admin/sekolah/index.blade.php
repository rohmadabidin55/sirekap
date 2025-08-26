@extends('layouts.admin')

@section('title', 'Data Sekolah')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6 md:p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Pengaturan Data Sekolah</h2>

    @if (session('status'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-lg" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('admin.sekolah.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama_sekolah" class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                    <input type="text" name="nama_sekolah" id="nama_sekolah" value="{{ old('nama_sekolah', $sekolah->nama_sekolah) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="npsn" class="block text-sm font-medium text-gray-700">NPSN</label>
                    <input type="text" name="npsn" id="npsn" value="{{ old('npsn', $sekolah->npsn) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Sekolah</label>
                <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">{{ old('alamat', $sekolah->alamat) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $sekolah->telepon) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $sekolah->email) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>

            <hr>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Logo Sekolah</label>
                    <img id="logoPreview" class="mt-2 h-24 w-auto rounded-md bg-gray-100 p-2" src="{{ $sekolah->logo ? asset('storage/' . $sekolah->logo) : 'https://placehold.co/300x100/E2E8F0/4A5568?text=Logo' }}" alt="Logo Preview">
                    <input type="file" name="logo" id="logo" class="mt-2 block w-full text-sm" onchange="document.getElementById('logoPreview').src = window.URL.createObjectURL(this.files[0])">
                    <small class="text-gray-500">Rekomendasi: PNG transparan, maks. 2MB.</small>
                </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-700">Favicon</label>
                    <img id="faviconPreview" class="mt-2 h-16 w-16 rounded-md bg-gray-100 p-2" src="{{ $sekolah->favicon ? asset('storage/' . $sekolah->favicon) : 'https://placehold.co/64x64/E2E8F0/4A5568?text=Fav' }}" alt="Favicon Preview">
                    <input type="file" name="favicon" id="favicon" class="mt-2 block w-full text-sm" onchange="document.getElementById('faviconPreview').src = window.URL.createObjectURL(this.files[0])">
                     <small class="text-gray-500">Rekomendasi: ICO/PNG, 32x32px, maks. 512KB.</small>
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
