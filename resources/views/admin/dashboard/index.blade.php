@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h3 class="text-gray-700 text-3xl font-medium">Dashboard</h3>

    <!-- Kartu Statistik -->
    <div class="mt-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-md border border-gray-200 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <h4 class="text-gray-500 text-sm font-medium">Jumlah Guru</h4>
                    <p class="text-3xl font-semibold text-gray-800">{{ $jumlahGuru }}</p>
                </div>
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
            </div>
            <div class="bg-white rounded-md border border-gray-200 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <h4 class="text-gray-500 text-sm font-medium">Jumlah Siswa</h4>
                    <p class="text-3xl font-semibold text-gray-800">{{ $jumlahSiswa }}</p>
                </div>
                 <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-1a6 6 0 00-1.78-4.125a4 4 0 00-6.44 0A6 6 0 003 20v1h12z"></path></svg>
                </div>
            </div>
            <div class="bg-white rounded-md border border-gray-200 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <h4 class="text-gray-500 text-sm font-medium">Jumlah Kelas</h4>
                    <p class="text-3xl font-semibold text-gray-800">{{ $jumlahKelas }}</p>
                </div>
                 <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
            <div class="bg-white rounded-md border border-gray-200 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <h4 class="text-gray-500 text-sm font-medium">Jumlah Pelajaran</h4>
                    <p class="text-3xl font-semibold text-gray-800">{{ $jumlahMapel }}</p>
                </div>
                 <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-md border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700">Grafik Rata-Rata Nilai</h3>
            <canvas id="grafikNilai" class="mt-4"></canvas>
        </div>
        <div class="bg-white p-6 rounded-md border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700">Grafik Kehadiran Siswa</h3>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <div class="relative mx-auto" style="max-width: 250px;">
                    <canvas id="grafikKehadiran"></canvas>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>Hadir</span>
                        <span class="font-semibold">{{ $rekapKehadiran['Hadir'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>Sakit</span>
                        <span class="font-semibold">{{ $rekapKehadiran['Sakit'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>Izin</span>
                        <span class="font-semibold">{{ $rekapKehadiran['Izin'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>Alpa</span>
                        <span class="font-semibold">{{ $rekapKehadiran['Alpa'] ?? 0 }}</span>
                    </div>
                     <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-purple-500 mr-2"></span>PMS</span>
                        <span class="font-semibold">{{ $rekapKehadiran['PMS'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Informasi Guru -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-md border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700">Guru Belum Input Nilai</h3>
            
            <!-- Form Filter Tanggal -->
            <form action="{{ route('admin.dashboard') }}" method="GET" class="mt-2 mb-4">
                <label for="tanggal_filter" class="text-sm text-gray-600">Pilih Tanggal:</label>
                <div class="flex space-x-2 mt-1">
                    <input type="date" name="tanggal_filter" id="tanggal_filter" value="{{ $tanggalFilter }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">Cek</button>
                </div>
            </form>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full">
                    <tbody class="bg-white">
                        @forelse($guruBelumInputNilai as $guru)
                        <tr>
                            <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ $guru->user->photo ? asset('storage/' . $guru->user->photo) : 'https://placehold.co/100x100/E2E8F0/4A5568?text=G' }}" alt="">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $guru->user->name }}</div>
                                        <div class="text-xs text-gray-500">NIP: {{ $guru->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-2 py-4 text-sm text-gray-500">Semua guru aktif sudah menginputkan nilai pada tanggal ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white p-6 rounded-md border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700">Guru Belum Terjadwal</h3>
             <div class="mt-4 overflow-x-auto">
                <table class="min-w-full">
                    <tbody class="bg-white">
                        @forelse($guruBelumTerjadwal as $guru)
                        <tr>
                            <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ $guru->user->photo ? asset('storage/' . $guru->user->photo) : 'https://placehold.co/100x100/E2E8F0/4A5568?text=G' }}" alt="">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $guru->user->name }}</div>
                                        <div class="text-xs text-gray-500">NIP: {{ $guru->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                             <td class="px-2 py-4 text-sm text-gray-500">Semua guru sudah memiliki jadwal.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data dari Controller
    const dataNilai = {!! Illuminate\Support\Js::from($rekapNilai) !!};
    const dataKehadiran = {!! Illuminate\Support\Js::from($rekapKehadiran) !!};

    // Grafik Rata-Rata Nilai (Bar Chart)
    if (Object.keys(dataNilai).length > 0) {
        const ctxNilai = document.getElementById('grafikNilai').getContext('2d');
        new Chart(ctxNilai, {
            type: 'bar',
            data: {
                labels: Object.keys(dataNilai),
                datasets: [{
                    label: 'Rata-Rata Nilai',
                    data: Object.values(dataNilai),
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, max: 100 } },
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    }

    // Grafik Kehadiran (Doughnut Chart)
    if (Object.keys(dataKehadiran).length > 0) {
        const ctxKehadiran = document.getElementById('grafikKehadiran').getContext('2d');
        new Chart(ctxKehadiran, {
            type: 'doughnut',
            data: {
                labels: Object.keys(dataKehadiran),
                datasets: [{
                    label: 'Rekap Kehadiran',
                    data: Object.values(dataKehadiran),
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.9)',    // Hadir
                        'rgba(234, 179, 8, 0.9)',    // Sakit
                        'rgba(59, 130, 246, 0.9)',   // Izin
                        'rgba(239, 68, 68, 0.9)',    // Alpa
                        'rgba(168, 85, 247, 0.9)'    // PMS
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: { 
                    legend: { 
                        display: false // Legenda dinonaktifkan karena sudah ada di samping
                    } 
                }
            }
        });
    }
});
</script>
@endsection
