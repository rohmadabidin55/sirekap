@extends('layouts.guru')

@section('title', 'Rekap Nilai Mata Pelajaran')

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
        @if($isGuruAsuh)
        <a href="{{ route('guru.rekap.anakasuh.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200">
            Rekap Nilai Anak Asuh &rarr;
        </a>
        @endif
    </div>

    <!-- Form Filter -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Filter Laporan Nilai</h3>
        <form action="{{ route('guru.rekap.mapel.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div class="xl:col-span-1">
                    <label for="bulan" class="text-sm">Bulan</label>
                    <input type="month" name="bulan" id="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="xl:col-span-1">
                    <label for="mapel_id" class="text-sm">Mata Pelajaran</label>
                    <select name="mapel_id" id="mapel_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Mapel</option>
                         @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="xl:col-span-1">
                    <label for="jurusan_id" class="text-sm">Jurusan</label>
                    <select name="jurusan_id" id="jurusan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="xl:col-span-1">
                    <label for="kelas_id" class="text-sm">Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                 <div class="xl:col-span-1">
                    <label for="siswa_nama" class="text-sm">Nama Siswa</label>
                    <input type="text" name="siswa_nama" id="siswa_nama" value="{{ request('siswa_nama') }}" placeholder="Cari nama..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="self-end">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Tampilkan Laporan</button>
                </div>
            </div>
        </form>
    </div>

    @if(request()->filled(['bulan', 'mapel_id']))
    <!-- Tabel Rekap -->
    <div class="mt-6 flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider sticky left-0 bg-gray-100" rowspan="2">No</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider sticky left-10 bg-gray-100" rowspan="2">NIS</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider sticky left-28 bg-gray-100" rowspan="2">Nama Siswa</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-center text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" colspan="{{ $daysInMonth }}">Nilai Bulan {{ $selectedMonth->translatedFormat('F Y') }}</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-center text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">Rata-Rata</th>
                        </tr>
                        <tr>
                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider" style="min-width: 50px;">{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($siswas as $index => $siswa)
                        <tr>
                            <td class="px-2 py-2 border-b border-gray-200 sticky left-0 bg-white">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border-b border-gray-200 sticky left-10 bg-white">{{ $siswa->nis }}</td>
                            <td class="px-4 py-2 border-b border-gray-200 sticky left-28 bg-white">{{ $siswa->user->name }}</td>
                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                @php
                                    $nilai = $nilaiHarian->get($siswa->id, collect())->get($i);
                                @endphp
                                <td class="px-2 py-2 border-b border-gray-200 text-center font-semibold">
                                    {{ $nilai->nilai_tugas ?? '-' }}
                                </td>
                            @endfor
                            <td class="px-4 py-2 border-b border-gray-200 text-center font-bold bg-gray-50">
                                {{ $rataRataNilai->get($siswa->id) ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 4 + $daysInMonth }}" class="text-center py-4 text-gray-500">Tidak ada data siswa yang cocok dengan filter yang dipilih.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @else
    <div class="mt-6 text-center p-8 border-2 border-dashed border-gray-300 rounded-lg text-gray-500">
        <p>Silakan lengkapi filter di atas dan klik tombol "Tampilkan Laporan" untuk melihat data.</p>
    </div>
    @endif

@endsection
