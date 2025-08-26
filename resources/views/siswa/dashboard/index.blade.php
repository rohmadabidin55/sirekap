@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

@section('content')
    <!-- Header: Info Siswa, Jam, dan Tanggal -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="flex items-center">
                <img class="h-20 w-20 rounded-full object-cover ring-4 ring-indigo-100" src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://placehold.co/100x100/E2E8F0/4A5568?text=S' }}" alt="Foto Siswa">
                <div class="ml-5">
                    <h3 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h3>
                    <p class="text-gray-500">{{ $siswa->kelas->jurusan->nama_jurusan }} | {{ $siswa->kelas->nama_kelas }}</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 text-left md:text-right" x-data="{ waktu: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }), tanggal: new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }" x-init="setInterval(() => { waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }); }, 1000)">
                <p class="text-3xl font-bold text-indigo-600" x-text="waktu"></p>
                <p class="text-gray-500" x-text="tanggal"></p>
            </div>
        </div>
    </div>

    <!-- Form Filter -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <form action="{{ route('siswa.dashboard') }}" method="GET">
            <div class="flex items-end space-x-4">
                <div class="flex-grow">
                    <label for="bulan" class="text-sm font-medium text-gray-700">Tampilkan Laporan Bulan</label>
                    <input type="month" name="bulan" id="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Rekap -->
    <div class="mt-6 flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">No</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">Tanggal</th>
                            @foreach($mapels as $mapel)
                                <th class="px-4 py-3 border-b border-gray-200 text-center text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" colspan="2" style="min-width: 150px;">{{ $mapel->nama_mapel }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($mapels as $mapel)
                                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Absen</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($dailyRecords as $record)
                        <tr>
                            <td class="px-4 py-2 border-b border-gray-200">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border-b border-gray-200">{{ \Carbon\Carbon::parse($record['tanggal'])->format('d-m-Y') }}</td>
                            @foreach($mapels as $mapel)
                                @php
                                    $nilai = $record['nilai'][$mapel->id] ?? '-';
                                    $presensi = $record['presensi'][$mapel->id] ?? '-';
                                @endphp
                                <td class="px-2 py-2 border-b border-gray-200 text-center font-semibold">{{ $nilai }}</td>
                                <td class="px-2 py-2 border-b border-gray-200 text-center text-xs">
                                    @if($presensi == 'Hadir') <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $presensi }}</span>
                                    @elseif($presensi == 'Sakit') <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $presensi }}</span>
                                    @elseif($presensi == 'Izin') <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $presensi }}</span>
                                    @elseif($presensi == 'Alpa') <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $presensi }}</span>
                                    @elseif($presensi == 'PMS') <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">{{ $presensi }}</span>
                                    @else {{ $presensi }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 2 + ($mapels->count() * 2) }}" class="text-center py-4 text-gray-500">Tidak ada data nilai atau kehadiran pada bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-md border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700">Grafik Rata-Rata Nilai (Bulan Ini)</h3>
            <canvas id="grafikNilai" class="mt-4"></canvas>
        </div>
        <div class="bg-white p-6 rounded-md border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700">Grafik Kehadiran (Bulan Ini)</h3>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <div class="relative mx-auto" style="max-width: 250px;">
                    <canvas id="grafikKehadiran"></canvas>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>Hadir</span>
                        <span class="font-semibold">{{ $rekapPresensiGrafik['Hadir'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>Sakit</span>
                        <span class="font-semibold">{{ $rekapPresensiGrafik['Sakit'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>Izin</span>
                        <span class="font-semibold">{{ $rekapPresensiGrafik['Izin'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>Alpa</span>
                        <span class="font-semibold">{{ $rekapPresensiGrafik['Alpa'] ?? 0 }}</span>
                    </div>
                     <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-purple-500 mr-2"></span>PMS</span>
                        <span class="font-semibold">{{ $rekapPresensiGrafik['PMS'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataNilai = {!! $rekapNilaiGrafik->toJson() !!};
    const dataKehadiran = {!! $rekapPresensiGrafik->toJson() !!};

    if (Object.keys(dataNilai).length > 0) {
        const ctxNilai = document.getElementById('grafikNilai').getContext('2d');
        new Chart(ctxNilai, { type: 'bar', data: { labels: Object.keys(dataNilai), datasets: [{ label: 'Rata-Rata Nilai', data: Object.values(dataNilai), backgroundColor: 'rgba(79, 70, 229, 0.8)' }] }, options: { scales: { y: { beginAtZero: true, max: 100 } }, plugins: { legend: { display: false } } } });
    }
    
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
