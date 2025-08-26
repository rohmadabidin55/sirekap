@extends('layouts.admin')

@section('title', 'Rekap Laporan Per Guru')

@section('content')
    <h3 class="text-gray-700 text-3xl font-medium">Rekap Laporan Per Guru</h3>

    <!-- Form Filter -->
    <div class="mt-6 bg-white p-4 rounded-md shadow-sm border border-gray-200">
        <form action="{{ route('admin.rekap.perguru.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label for="bulan" class="text-sm">Bulan</label>
                    <input type="month" name="bulan" id="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                 <div>
                    <label for="guru_id" class="text-sm">Guru</label>
                    <select name="guru_id" id="guru_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Guru</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}" {{ request('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->user->name }}</option>
                        @endforeach
                    </select>
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
                    <label for="mapel_id" class="text-sm">Mata Pelajaran</label>
                    <select name="mapel_id" id="mapel_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Mapel</option>
                         @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="perPage" class="text-sm">Per Halaman</label>
                    <select name="perPage" id="perPage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach(['10', '25', '50', '100'] as $val)
                            <option value="{{ $val }}" {{ request('perPage', 10) == $val ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="self-end">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Tampilkan Laporan</button>
                </div>
            </div>
        </form>
    </div>

    @if(request()->filled(['bulan', 'guru_id', 'mapel_id']))
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
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">Guru Asuh</th>
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
                            <td class="px-2 py-2 border-b border-gray-200 sticky left-0 bg-white">{{ $siswas->firstItem() + $index }}</td>
                            <td class="px-4 py-2 border-b border-gray-200 sticky left-10 bg-white">{{ $siswa->nis }}</td>
                            <td class="px-4 py-2 border-b border-gray-200 sticky left-28 bg-white">{{ $siswa->user->name }}</td>
                            <td class="px-4 py-2 border-b border-gray-200">{{ $siswa->guruAsuh->guru->user->name ?? '-' }}</td>
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
                            <td colspan="{{ 5 + $daysInMonth }}" class="text-center py-4 text-gray-500">Tidak ada data siswa yang cocok dengan filter yang dipilih.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $siswas->links() }}
    </div>
    @else
    <div class="mt-6 text-center p-8 border-2 border-dashed border-gray-300 rounded-lg text-gray-500">
        <p>Silakan lengkapi filter di atas dan klik tombol "Tampilkan Laporan" untuk melihat data.</p>
    </div>
    @endif
@endsection
