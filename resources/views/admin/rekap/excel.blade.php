<table>
    <thead>
        <tr>
            <th colspan="{{ 5 + (count($mapels) * 2) }}" style="font-weight: bold; text-align: center; font-size: 14px;">REKAP LAPORAN SISWA</th>
        </tr>
        <tr>
            <th colspan="{{ 5 + (count($mapels) * 2) }}" style="font-weight: bold; text-align: center; font-size: 12px;">Bulan: {{ $selectedMonth->translatedFormat('F Y') }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">No</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">Nama Siswa</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">Kelas</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">Guru Asuh</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">Tanggal</th>
            @foreach($mapels as $mapel)
                <th colspan="2" style="font-weight: bold; border: 1px solid #000; text-align: center;">{{ $mapel->nama_mapel }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach($mapels as $mapel)
                <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Nilai</th>
                <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Absen</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($dailyRecords as $record)
        <tr>
            <td style="border: 1px solid #000;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid #000;">{{ $record['siswa']->user->name }}</td>
            <td style="border: 1px solid #000;">{{ $record['siswa']->kelas->nama_kelas }}</td>
            <td style="border: 1px solid #000;">{{ $record['siswa']->guruAsuh->guru->user->name ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($record['tanggal'])->format('d-m-Y') }}</td>
            @foreach($mapels as $mapel)
                @php
                    $nilai = $record['nilai'][$mapel->id] ?? '-';
                    $presensi = $record['presensi'][$mapel->id] ?? '-';
                @endphp
                <td style="border: 1px solid #000; text-align: center;">{{ $nilai }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $presensi }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
