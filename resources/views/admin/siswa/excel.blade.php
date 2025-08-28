<table>
    <thead>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">NIS</th>
            <th style="font-weight: bold; border: 1px solid #000;">NISN</th>
            <th style="font-weight: bold; border: 1px solid #000;">Nama Lengkap</th>
            <th style="font-weight: bold; border: 1px solid #000;">Email</th>
            <th style="font-weight: bold; border: 1px solid #000;">Kelas</th>
            <th style="font-weight: bold; border: 1px solid #000;">Jurusan</th>
            <th style="font-weight: bold; border: 1px solid #000;">Alamat</th>
            <th style="font-weight: bold; border: 1px solid #000;">No. Telepon Orang Tua</th>
        </tr>
    </thead>
    <tbody>
        @foreach($siswas as $siswa)
        <tr>
            <td style="border: 1px solid #000;">{{ $siswa->nis }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->nisn }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->user->name }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->user->email }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->kelas->nama_kelas }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->kelas->jurusan->nama_jurusan }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->alamat }}</td>
            <td style="border: 1px solid #000;">{{ $siswa->no_telepon_orang_tua }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
