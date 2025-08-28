<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Anak Asuh</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 16px; margin: 0; }
        .header p { font-size: 12px; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-xs { font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAPITULASI NILAI DAN PRESENSI ANAK ASUH</h1>
        <p>
            Bulan: {{ $selectedMonth->translatedFormat('F Y') }} <br>
            Wali Kelas: {{ $guru->user->name }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Siswa</th>
                <th rowspan="2">Kelas</th>
                <th rowspan="2">Tanggal</th>
                @foreach($mapels as $mapel)
                    <th colspan="2" class="text-center">{{ $mapel->nama_mapel }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($mapels as $mapel)
                    <th class="text-center">Nilai</th>
                    <th class="text-center">Absen</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($dailyRecords as $record)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $record['siswa']->user->name }}</td>
                <td>{{ $record['siswa']->kelas->nama_kelas }}</td>
                <td>{{ \Carbon\Carbon::parse($record['tanggal'])->format('d-m-Y') }}</td>
                @foreach($mapels as $mapel)
                    @php
                        $nilai = $record['nilai'][$mapel->id] ?? '-';
                        $presensi = $record['presensi'][$mapel->id] ?? '-';
                    @endphp
                    <td class="text-center">{{ $nilai }}</td>
                    <td class="text-center text-xs">{{ $presensi }}</td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ 4 + ($mapels->count() * 2) }}" class="text-center">Tidak ada data untuk ditampilkan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
