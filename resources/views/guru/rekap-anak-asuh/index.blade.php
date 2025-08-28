@extends('layouts.guru')

@section('title', 'Rekap Nilai Anak Asuh')

@section('content')
    <!-- Header: Info Guru, Jam, dan Tanggal -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="flex items-center">
                <img class="h-20 w-20 rounded-full object-cover ring-4 ring-indigo-100" src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://placehold.co/100x100/E2E8F0/4A5568?text=G' }}" alt="Foto Guru">
                <div class="ml-5">
                    <h3 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h3>
                    <p class="text-gray-500">NIP: {{ $guru->nip }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Navigasi -->
    <div class="mb-8 flex flex-wrap gap-4">
        <a href="{{ route('guru.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            &larr; Kembali ke Dashboard
        </a>
        <a href="{{ route('guru.rekap.mapel.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
            Rekap Nilai Mapel &rarr;
        </a>
    </div>

    <!-- Form Filter -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Filter Laporan Nilai Anak Asuh</h3>
        <form action="{{ route('guru.rekap.anakasuh.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="bulan" class="text-sm">Bulan</label>
                    <input type="month" name="bulan" id="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="jurusan_id" class="text-sm">Jurusan</label>
                    <select name="jurusan_id" id="jurusan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="kelas_id" class="text-sm">Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="siswa_nama" class="text-sm">Nama Siswa</label>
                    <input type="text" name="siswa_nama" id="siswa_nama" value="{{ request('siswa_nama') }}" placeholder="Cari nama..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="self-end flex space-x-2">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
                    @if(request()->filled('bulan'))
                    <a href="{{ route('guru.rekap.anakasuh.pdf', request()->query()) }}" target="_blank" class="w-full flex justify-center items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        PDF
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if(request()->filled('bulan'))
    <!-- Tabel Rekap -->
    <div class="mt-6 flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">No</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">Nama Siswa</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">Kelas</th>
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
                            <td class="px-4 py-2 border-b border-gray-200">{{ $record['siswa']->user->name }}</td>
                            <td class="px-4 py-2 border-b border-gray-200">{{ $record['siswa']->kelas->nama_kelas }}</td>
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
                                    @else {{ $presensi }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 4 + ($mapels->count() * 2) }}" class="text-center py-4 text-gray-500">Tidak ada data untuk ditampilkan sesuai filter yang dipilih.</td>
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
            <h3 class="text-lg font-semibold text-gray-700">Grafik Rata-Rata Nilai (Berdasarkan Filter)</h3>
            <canvas id="grafikNilai" class="mt-4"></canvas>
        </div>
        <div class="bg-white p-6 rounded-md border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700">Grafik Kehadiran (Berdasarkan Filter)</h3>
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
    @else
    <div class="mt-6 text-center p-8 border-2 border-dashed border-gray-300 rounded-lg text-gray-500">
        <p>Silakan pilih bulan dan filter lainnya, lalu klik "Tampilkan Laporan" untuk melihat data.</p>
    </div>
    @endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.register(ChartDataLabels);

    const dataNilai = {!! $rekapNilaiGrafik->toJson() !!};
    const dataKehadiran = {!! $rekapPresensiGrafik->toJson() !!};

    if (Object.keys(dataNilai).length > 0) {
        const ctxNilai = document.getElementById('grafikNilai').getContext('2d');
        new Chart(ctxNilai, { 
            type: 'bar', 
            data: { 
                labels: Object.keys(dataNilai), 
                datasets: [{ 
                    label: 'Rata-Rata Nilai', 
                    data: Object.values(dataNilai), 
                    backgroundColor: 'rgba(79, 70, 229, 0.8)' 
                }] 
            }, 
            options: { 
                scales: { y: { beginAtZero: true, max: 100 } }, 
                plugins: { 
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#4b5563',
                        font: {
                            weight: 'bold'
                        }
                    }
                } 
            } 
        });
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
                        display: false 
                    } 
                } 
            } 
        });
    }
});
</script>
@endsection
