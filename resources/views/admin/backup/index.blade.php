@extends('layouts.admin')

@section('title', 'Backup & Restore')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-center">
        <h3 class="text-gray-700 text-3xl font-medium">Backup & Restore Database</h3>
        <form action="{{ route('admin.backup.create') }}" method="POST">
            @csrf
            <button type="submit" class="mt-4 sm:mt-0 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Buat Backup Baru
            </button>
        </form>
    </div>

    @if (session('status'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-lg" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mt-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabel Data Backup -->
    <div class="mt-6 flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nama File</th>
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($backups as $backup)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $backup['file_name'] }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $backup['file_size'] }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $backup['last_modified'] }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium">
                                    <div class="flex justify-center items-center space-x-4">
                                        <a href="{{ route('admin.backup.download', $backup['file_name']) }}" class="text-blue-600 hover:text-blue-900" title="Unduh">
                                            Unduh
                                        </a>
                                        <form action="{{ route('admin.backup.destroy', $backup['file_name']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file backup ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada file backup.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Fitur Restore -->
    <div class="mt-8 bg-white p-6 rounded-md shadow-sm border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-700">Restore Database</h3>
        <div class="mt-4 p-4 bg-red-100 text-red-800 border-l-4 border-red-500">
            <p class="font-bold">Perhatian!</p>
            <p class="text-sm">Fitur restore database dari file unggahan belum diimplementasikan. Proses restore sangat berisiko dan disarankan untuk dilakukan secara manual melalui command line oleh administrator sistem.</p>
        </div>
    </div>
@endsection
