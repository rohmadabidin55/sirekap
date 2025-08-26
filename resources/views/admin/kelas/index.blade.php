@extends('layouts.admin')

@section('title', 'Manajemen Kelas')

@section('content')
<div x-data="kelasCrud()">
    <div class="flex flex-col sm:flex-row justify-between items-center">
        <h3 class="text-gray-700 text-3xl font-medium">Data Kelas</h3>
        <button @click="openModal()" class="mt-4 sm:mt-0 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Tambah Kelas
        </button>
    </div>
    
    <div x-show="successMessage" x-text="successMessage" x-transition class="mt-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-md"></div>

    <!-- Form Filter dan Pagination (dengan Live Search) -->
    <div class="mt-6 bg-white p-4 rounded-md shadow-sm border border-gray-200">
        <form x-data action="{{ route('admin.kelas.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    <label for="search" class="text-sm">Cari Nama Kelas atau Jurusan</label>
                    <input type="text" name="search" id="search" placeholder="Ketik untuk mencari..." value="{{ request('search') }}" 
                           @input.debounce.500ms="$el.form.submit()"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="perPage" class="text-sm">Tampilkan per Halaman</label>
                    <select name="perPage" id="perPage" @change="$el.form.submit()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach(['10', '25', '50', '100'] as $val)
                            <option value="{{ $val }}" {{ request('perPage', 10) == $val ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="flex flex-col mt-6">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nama Kelas</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Tingkat</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($kelasList as $kelas)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $kelas->nama_kelas }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $kelas->tingkat }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $kelas->jurusan->nama_jurusan }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium">
                                    <div class="flex justify-center items-center space-x-4">
                                        <button @click="edit({{ $kelas }})" class="text-gray-500 hover:text-indigo-600" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <button @click="openDeleteModal({{ $kelas->id }})" class="text-gray-500 hover:text-red-600" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $kelasList->links() }}
    </div>

    <!-- Modal Form -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="closeModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all">
            <form @submit.prevent="saveKelas()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="isEditMode ? 'Edit Kelas' : 'Tambah Kelas'"></h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="nama_kelas" class="block text-sm font-medium text-gray-700">Nama Kelas</label>
                            <input type="text" x-model="formData.nama_kelas" id="nama_kelas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required placeholder="Contoh: 12 RPL 1">
                            <span x-show="errors.nama_kelas" x-text="errors.nama_kelas ? errors.nama_kelas[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                        <div>
                            <label for="tingkat" class="block text-sm font-medium text-gray-700">Tingkat</label>
                            <select x-model="formData.tingkat" id="tingkat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                            <span x-show="errors.tingkat" x-text="errors.tingkat ? errors.tingkat[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                        <div>
                            <label for="jurusan_id" class="block text-sm font-medium text-gray-700">Jurusan</label>
                            <select x-model="formData.jurusan_id" id="jurusan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                @endforeach
                            </select>
                            <span x-show="errors.jurusan_id" x-text="errors.jurusan_id ? errors.jurusan_id[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                    <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="isDeleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="closeDeleteModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all">
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Hapus Data Kelas</h3>
                <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin? Menghapus kelas akan berpengaruh pada data siswa.</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-center space-x-3">
                <button @click="closeDeleteModal()" type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Batal</button>
                <button @click="deleteKelas()" type="button" class="inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    function kelasCrud() {
        return {
            isModalOpen: false,
            isDeleteModalOpen: false,
            kelasIdToDelete: null,
            isEditMode: false,
            successMessage: '',
            formData: {},
            errors: {},
            
            init() { this.resetForm(); },
            
            openModal() { this.resetForm(); this.isModalOpen = true; },
            closeModal() { this.isModalOpen = false; },
            resetForm() {
                this.formData = { id: null, nama_kelas: '', tingkat: '', jurusan_id: '' };
                this.isEditMode = false;
                this.errors = {};
            },

            edit(kelas) {
                this.formData = {
                    id: kelas.id,
                    nama_kelas: kelas.nama_kelas,
                    tingkat: kelas.tingkat,
                    jurusan_id: kelas.jurusan_id
                };
                this.isEditMode = true;
                this.isModalOpen = true;
            },

            async saveKelas() {
                this.errors = {};
                let url = this.isEditMode ? `/admin/kelas/${this.formData.id}` : '{{ route("admin.kelas.store") }}';
                let method = this.isEditMode ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(this.formData)
                });

                if (response.ok) {
                    window.location.reload();
                } else if (response.status === 422) {
                    const result = await response.json();
                    this.errors = result.errors;
                } else { console.error('An error occurred'); }
            },

            openDeleteModal(id) { this.kelasIdToDelete = id; this.isDeleteModalOpen = true; },
            closeDeleteModal() { this.isDeleteModalOpen = false; this.kelasIdToDelete = null; },

            async deleteKelas() {
                if (!this.kelasIdToDelete) return;
                const response = await fetch(`/admin/kelas/${this.kelasIdToDelete}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    console.error('Gagal menghapus');
                }
            },
        }
    }
</script>
@endsection
