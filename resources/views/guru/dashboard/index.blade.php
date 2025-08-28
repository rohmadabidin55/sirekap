@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
<div x-data="nilaiHandler()">
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
            <div class="mt-4 md:mt-0 text-left md:text-right">
                <p class="text-3xl font-bold text-indigo-600" x-text="waktu"></p>
                <p class="text-gray-500" x-text="tanggal"></p>
            </div>
        </div>
    </div>

    <!-- Menu Rekap -->
    <div class="mb-8 grid grid-cols-1 {{ $isGuruAsuh ? 'sm:grid-cols-2' : '' }} gap-6">
        <a href="{{ route('guru.rekap.mapel.index') }}" class="block text-center bg-white p-6 rounded-xl shadow-md border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-300">
            <h4 class="font-semibold text-lg text-gray-800">Rekap Nilai Mapel</h4>
            <p class="text-sm text-gray-500 mt-1">Lihat rekapitulasi nilai mata pelajaran yang Anda ajar.</p>
        </a>
        @if($isGuruAsuh)
        <a href="{{ route('guru.rekap.anakasuh.index') }}" class="block text-center bg-white p-6 rounded-xl shadow-md border border-gray-200 hover:border-green-500 hover:bg-green-50 transition-all duration-300">
            <h4 class="font-semibold text-lg text-gray-800">Rekap Nilai Anak Asuh</h4>
            <p class="text-sm text-gray-500 mt-1">Lihat rekapitulasi nilai siswa yang Anda bimbing.</p>
        </a>
        @endif
    </div>


    <!-- Form Pilihan dan Tabel Nilai -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Input Nilai & Kehadiran Siswa</h3>
        
        <!-- Filter -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label for="tanggal_input" class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" id="tanggal_input" x-model="tanggalInput" @change="fetchSiswa()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="mapel" class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                <select id="mapel" x-model="selectedMapel" @change="updateKelasOptions()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($mapelDanKelas as $item)
                        <option value="{{ $item['mata_pelajaran']->id }}">{{ $item['mata_pelajaran']->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="kelas" class="block text-sm font-medium text-gray-700">Kelas</label>
                <select id="kelas" x-model="selectedKelas" @change="fetchSiswa()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :disabled="!selectedMapel">
                    <option value="">Pilih Kelas</option>
                    <template x-for="kelas in kelasOptions" :key="kelas.id">
                        <option :value="kelas.id" x-text="kelas.nama_kelas"></option>
                    </template>
                </select>
            </div>
        </div>

        <!-- Tabel Nilai & Kehadiran -->
        <div class="overflow-x-auto">
            <div x-show="loading" class="text-center p-4">
                <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
            <div x-show="!loading && siswas.length > 0">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 bg-gray-50 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 150px;">Kehadiran</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 bg-gray-50 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 150px;">Nilai Harian</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <template x-for="(siswa, index) in siswas" :key="siswa.id">
                            <tr>
                                <td class="px-6 py-4 border-b border-gray-200" x-text="siswa.user.name"></td>
                                <td class="px-6 py-4 border-b border-gray-200 text-center">
                                    <select x-model="siswa.kehadiran" class="w-28 text-center rounded-lg border-gray-300 shadow-sm">
                                        <option value="Hadir">Hadir</option>
                                        <option value="Sakit">Sakit</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Alpa">Alpa</option>
                                        <option value="PMS">PMS</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200 text-center">
                                    <input type="number" min="0" max="100" 
                                           x-model="siswa.nilai_harian"
                                           @paste.prevent="handlePaste(index, $event)"
                                           class="w-24 text-center rounded-lg border-gray-300 shadow-sm">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <!-- Tombol Simpan -->
                <div class="flex justify-end mt-6">
                    <button @click="saveAllChanges()" :disabled="saving" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg x-show="saving" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
                 <div x-show="successMessage" x-text="successMessage" class="mt-4 text-green-600 text-right"></div>
            </div>
            <div x-show="!selectedKelas" class="text-center p-8 border-2 border-dashed border-gray-300 rounded-lg text-gray-500">
                <p>Silakan pilih Tanggal, Mata Pelajaran, dan Kelas untuk menampilkan data.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function nilaiHandler() {
        const mapelDanKelas = {!! json_encode($mapelDanKelas->values()) !!};
        return {
            waktu: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
            tanggal: new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }),
            tanggalInput: new Date().toISOString().slice(0, 10),
            selectedMapel: '',
            selectedKelas: '',
            kelasOptions: [],
            siswas: [],
            loading: false,
            saving: false,
            successMessage: '',
            
            init() { setInterval(() => { this.waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }); }, 1000); },
            updateKelasOptions() {
                this.selectedKelas = ''; this.siswas = [];
                if (!this.selectedMapel) { this.kelasOptions = []; return; }
                const penugasan = mapelDanKelas.find(item => item.mata_pelajaran.id == this.selectedMapel);
                this.kelasOptions = penugasan ? penugasan.kelas : [];
            },
            async fetchSiswa() {
                if (!this.selectedKelas || !this.selectedMapel || !this.tanggalInput) { this.siswas = []; return; }
                this.loading = true; this.successMessage = '';
                try {
                    const response = await fetch(`{{ route('guru.getSiswa') }}?kelas_id=${this.selectedKelas}&mapel_id=${this.selectedMapel}&tanggal=${this.tanggalInput}`);
                    this.siswas = await response.json();
                } catch (error) { console.error('Gagal mengambil data siswa:', error); } 
                finally { this.loading = false; }
            },

            // PERBAIKAN: Fungsi baru untuk menangani paste dari Excel
            handlePaste(startIndex, event) {
                let pasteData = (event.clipboardData || window.clipboardData).getData('text');
                let values = pasteData.split(/\r?\n/).filter(v => v.trim() !== '');

                values.forEach((value, i) => {
                    let targetIndex = startIndex + i;
                    if (this.siswas[targetIndex]) {
                        // Pastikan nilai yang ditempel adalah angka dan dalam rentang 0-100
                        let numericValue = parseInt(value, 10);
                        if (!isNaN(numericValue) && numericValue >= 0 && numericValue <= 100) {
                            this.siswas[targetIndex].nilai_harian = numericValue;
                        }
                    }
                });
            },

            async saveAllChanges() {
                if (this.siswas.length === 0) return;
                this.saving = true; this.successMessage = '';
                try {
                    const payload = this.siswas.map(s => ({ id: s.id, nilai_harian: s.nilai_harian, kehadiran: s.kehadiran }));
                    const response = await fetch('{{ route('guru.updateNilaiDanKehadiran') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({
                            mapel_id: this.selectedMapel,
                            tanggal: this.tanggalInput,
                            data: payload
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
