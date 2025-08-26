@extends('layouts.admin')

@section('title', 'Rekap Laporan')

@section('content')
    <h3 class="text-gray-700 text-3xl font-medium">Rekap Laporan Siswa</h3>

    <!-- Form Filter -->
    <div class="mt-6 bg-white p-4 rounded-md shadow-sm border border-gray-200">
        <form action="{{ route('admin.rekap.laporan.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="lg:col-span-1">
                    <label for="bulan" class="text-sm">Bulan</label>
                    <input type="month" name="bulan" id="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="lg:col-span-1">
                    <label for="jurusan_id" class="text-sm">Jurusan</label>
                    <select name="jurusan_id" id="jurusan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label for="kelas_id" class="text-sm">Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label for="guru_asuh_id" class="text-sm">Guru Asuh</label>
                    <select name="guru_asuh_id" id="guru_asuh_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Guru</option>
                         @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}" {{ request('guru_asuh_id') == $guru->id ? 'selected' : '' }}>{{ $guru->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label for="perPage" class="text-sm">Per Halaman</label>
                    <select name="perPage" id="perPage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach(['10', '25', '50', '100'] as $val)
                            <option value="{{ $val }}" {{ request('perPage', 10) == $val ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="self-end">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
                </div>
            </div>
        </form>
    </div>

    @if(request()->has('bulan') && request()->input('bulan') != '')
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
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">Guru Asuh</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">Tanggal</th>
                            @foreach($mapels as $mapel)
                                <th class="px-4 py-3 border-b border-gray-200 text-center text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" colspan="2">{{ $mapel->nama_mapel }}</th>
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
                            <td class="px-4 py-2 border-b border-gray-200">{{ $dailyRecords->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-2 border-b border-gray-200">{{ $record['siswa']->user->name }}</td>
                            <td class="px-4 py-2 border-b border-gray-200">{{ $record['siswa']->kelas->nama_kelas }}</td>
                            <td class="px-4 py-2 border-b border-gray-200">{{ $record['siswa']->guruAsuh->guru->user->name ?? '-' }}</td>
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
                            <td colspan="{{ 5 + ($mapels->count() * 2) }}" class="text-center py-4 text-gray-500">Tidak ada data untuk ditampilkan sesuai filter yang dipilih.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $dailyRecords->links() }}
    </div>

    <!-- Grafik -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-md border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700">Grafik Rata-Rata Nilai (Berdasarkan Filter)</h3>
            <canvas id="grafikNilai" class="mt-4"></canvas>
        </div>
        <div class="bg-white p-6 rounded-md border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700">Grafik Kehadiran Siswa (Berdasarkan Filter)</h3>
            <canvas id="grafikKehadiran" class="mt-4"></canvas>
        </div>
    </div>
    @else
    <div class="mt-6 text-center p-8 border-2 border-dashed border-gray-300 rounded-lg text-gray-500">
        <p>Silakan gunakan filter di atas dan klik tombol "Filter" untuk menampilkan laporan.</p>
    </div>
    @endif

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
        new Chart(ctxKehadiran, { type: 'doughnut', data: { labels: Object.keys(dataKehadiran), datasets: [{ label: 'Rekap Kehadiran', data: Object.values(dataKehadiran), backgroundColor: ['rgba(34, 197, 94, 0.8)', 'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(239, 68, 68, 0.8)'], hoverOffset: 4 }] }, options: { responsive: true, plugins: { legend: { position: 'top' } } } });
    }
});
</script>
@endsection
