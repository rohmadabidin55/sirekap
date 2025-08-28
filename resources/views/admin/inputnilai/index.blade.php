@extends('layouts.admin')

@section('title', 'Input Nilai & Presensi')

@section('content')
<div x-data="nilaiHandler()">
    <h3 class="text-gray-700 text-3xl font-medium">Input Nilai & Presensi Siswa</h3>

    <!-- Form Pilihan dan Tabel Nilai -->
    <div class="mt-6 bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Pilih Data</h3>
        
        <!-- Filter -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div>
                <label for="tanggal_input" class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" id="tanggal_input" x-model="tanggalInput" @change="fetchSiswa()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="jurusan_id" class="block text-sm font-medium text-gray-700">Jurusan</label>
                <select id="jurusan_id" x-model="selectedJurusan" @change="fetchSiswa()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">Pilih Jurusan</option>
                    @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="kelas_id" class="block text-sm font-medium text-gray-700">Kelas</label>
                <select id="kelas_id" x-model="selectedKelas" @change="fetchSiswa()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">Pilih Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tabel Nilai & Kehadiran -->
        <div class="overflow-x-auto">
            <div x-show="loading" class="text-center p-4">
                <p class="text-gray-500">Memuat data siswa...</p>
            </div>
            <div x-show="!loading && siswas.length > 0">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" rowspan="2">Nama Siswa</th>
                            @foreach($mapels as $mapel)
                                <th class="px-4 py-3 border-b border-gray-200 text-center text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" colspan="2">{{ $mapel->nama_mapel }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($mapels as $mapel)
                                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Absen</th>
                                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <template x-for="(siswa, index) in siswas" :key="siswa.id">
                            <tr>
                                <td class="px-4 py-2 border-b border-gray-200 whitespace-nowrap" x-text="siswa.user.name"></td>
                                @foreach($mapels as $mapel)
                                <td class="px-2 py-2 border-b border-gray-200 text-center">
                                    <select x-model="siswa.presensi[{{ $mapel->id }}]" class="w-24 text-center rounded-lg border-gray-300 shadow-sm text-xs">
                                        <option value="Hadir">Hadir</option>
                                        <option value="Sakit">Sakit</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Alpa">Alpa</option>
                                        <option value="PMS">PMS</option>
                                    </select>
                                </td>
                                <td class="px-2 py-2 border-b border-gray-200 text-center">
                                    <input type="number" min="0" max="100" 
                                           x-model="siswa.nilai[{{ $mapel->id }}]" 
                                           @paste.prevent="handlePaste(index, {{ $mapel->id }}, $event)"
                                           class="w-20 text-center rounded-lg border-gray-300 shadow-sm">
                                </td>
                                @endforeach
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div class="flex justify-end mt-6">
                    <button @click="saveAllChanges()" :disabled="saving" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
                 <div x-show="successMessage" x-text="successMessage" class="mt-4 text-green-600 text-right"></div>
            </div>
            <div x-show="!loading && siswas.length === 0 && selectedKelas" class="text-center p-4 text-gray-500">
                Tidak ada siswa di kelas ini.
            </div>
             <div x-show="!selectedKelas" class="text-center p-8 border-2 border-dashed border-gray-300 rounded-lg text-gray-500">
                <p>Silakan pilih Tanggal dan Kelas untuk menampilkan data.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function nilaiHandler() {
        return {
            tanggalInput: new Date().toISOString().slice(0, 10),
            selectedJurusan: '',
            selectedKelas: '',
            siswas: [],
            loading: false,
            saving: false,
            successMessage: '',
            
            async fetchSiswa() {
                if (!this.selectedKelas || !this.tanggalInput) { this.siswas = []; return; }
                this.loading = true; this.successMessage = '';
                try {
                    const response = await fetch(`{{ route('admin.inputnilai.getSiswa') }}?kelas_id=${this.selectedKelas}&tanggal=${this.tanggalInput}`);
                    this.siswas = await response.json();
                } catch (error) { console.error('Gagal mengambil data siswa:', error); } 
                finally { this.loading = false; }
            },

            handlePaste(startIndex, mapelId, event) {
                let pasteData = (event.clipboardData || window.clipboardData).getData('text');
                let values = pasteData.split(/\r?\n/).filter(v => v.trim() !== '');

                values.forEach((value, i) => {
                    let targetIndex = startIndex + i;
                    if (this.siswas[targetIndex]) {
                        let numericValue = parseInt(value, 10);
                        if (!isNaN(numericValue) && numericValue >= 0 && numericValue <= 100) {
                            this.siswas[targetIndex].nilai[mapelId] = numericValue;
                        }
                    }
                });
            },

            async saveAllChanges() {
                if (this.siswas.length === 0) return;
                this.saving = true; this.successMessage = '';
                try {
                    const response = await fetch('{{ route('admin.inputnilai.update') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({
                            kelas_id: this.selectedKelas,
                            tanggal: this.tanggalInput,
                            data: this.siswas
                        })
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.successMessage = result.message;
                        setTimeout(() => this.successMessage = '', 3000);
                    } else { alert('Gagal menyimpan: ' + result.message); }
                } catch (error) { console.error('Gagal menyimpan:', error); alert('Terjadi kesalahan saat menyimpan.'); }
                finally { this.saving = false; }
            }
        }
    }
</script>
@endsection
