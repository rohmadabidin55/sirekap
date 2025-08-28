<table>
    <thead>
        <tr>
            <th colspan="{{ 4 + $daysInMonth + 1 }}" style="font-weight: bold; text-align: center; font-size: 14px;">REKAP LAPORAN NILAI PER GURU</th>
        </tr>
        <tr>
            <th colspan="{{ 4 + $daysInMonth + 1 }}" style="text-align: center;">Bulan: {{ $selectedMonth->translatedFormat('F Y') }}</th>
        </tr>
        <tr>
            <th colspan="{{ 4 + $daysInMonth + 1 }}" style="text-align: center;">Guru: {{ $guru->user->name }} | Mapel: {{ $mapel->nama_mapel }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">No</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">NIS</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">Nama Siswa</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">Guru Asuh</th>
            <th colspan="{{ $daysInMonth }}" style="font-weight: bold; border: 1px solid #000; text-align: center;">Nilai Harian</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000;">Rata-Rata</th>
        </tr>
        <tr>
            @for ($i = 1; $i <= $daysInMonth; $i++)
                <th style="font-weight: bold; border: 1px solid #000; text-align: center;">{{ $i }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($siswas as $index => $siswa)
        <tr>
            <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->nis }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->user->name }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->guruAsuh->guru->user->name ?? '-' }}</td>
            @for ($i = 1; $i <= $daysInMonth; $i++)
                @php
                    $nilai = $nilaiHarian->get($siswa->id, collect())->get($i);
                @endphp
                <td style="border: 1px solid #000; text-align: center;">
                    {{ $nilai->nilai_tugas ?? '' }}
                </td>
            @endfor
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">
                {{ $rataRataNilai->get($siswa->id) ?? '' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
