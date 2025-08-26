@extends('layouts.admin')

@section('title', 'Manajemen Jurusan')

@section('content')
<div x-data="jurusanCrud()">
    <h3 class="text-gray-700 text-3xl font-medium">Data Jurusan</h3>
    
    <!-- Tombol Tambah dan Notifikasi -->
    <div class="mt-8">
        <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
            Tambah Jurusan
        </button>
        
        <!-- Notifikasi Sukses -->
        <div x-show="successMessage" x-text="successMessage" x-transition class="mt-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-md"></div>
    </div>

    <!-- Tabel Data -->
    <div class="flex flex-col mt-6">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nama Jurusan</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Singkatan</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <template x-for="(jurusan, index) in jurusans" :key="jurusan.id">
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200" x-text="index + 1"></td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200" x-text="jurusan.nama_jurusan"></td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200" x-text="jurusan.singkatan"></td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium">
                                    <div class="flex justify-center items-center space-x-4">
                                        <button @click="edit(jurusan)" class="text-gray-500 hover:text-indigo-600" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button @click="openDeleteModal(jurusan.id)" class="text-gray-500 hover:text-red-600" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                         <tr x-show="jurusans.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data untuk ditampilkan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div @click="closeModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all">
            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" id="modal-title" x-text="isEditMode ? 'Edit Jurusan' : 'Tambah Jurusan'"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form @submit.prevent="saveJurusan()">
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label for="nama_jurusan" class="block text-sm font-medium text-gray-700">Nama Jurusan</label>
                            <input type="text" x-model="formData.nama_jurusan" id="nama_jurusan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <span x-show="errors.nama_jurusan" x-text="errors.nama_jurusan ? errors.nama_jurusan[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                        <div>
                            <label for="singkatan" class="block text-sm font-medium text-gray-700">Singkatan</label>
                            <input type="text" x-model="formData.singkatan" id="singkatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                             <span x-show="errors.singkatan" x-text="errors.singkatan ? errors.singkatan[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200">
                    <button type="button" @click="closeModal()" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="isDeleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="closeDeleteModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all">
            <div class="p-6">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Hapus Data Jurusan</h3>
                    <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-center space-x-3">
                <button @click="closeDeleteModal()" type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Batal</button>
                <button @click="deleteJurusan()" type="button" class="inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    function jurusanCrud() {
        return {
            jurusans: [],
            isModalOpen: false,
            isDeleteModalOpen: false,
            jurusanIdToDelete: null,
            isEditMode: false,
            successMessage: '',
            formData: {
                id: null,
                nama_jurusan: '',
                singkatan: ''
            },
            errors: {},
            
            init() {
                this.fetchJurusans();
            },
            
            fetchJurusans() {
                // PERBAIKAN DI SINI: Tambahkan header 'X-Requested-With'
                fetch('{{ route("admin.jurusan.index") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    this.jurusans = data.data;
                });
            },

            openModal() {
                this.resetForm();
                this.isModalOpen = true;
            },

            closeModal() {
                this.isModalOpen = false;
            },

            resetForm() {
                this.formData = { id: null, nama_jurusan: '', singkatan: '' };
                this.isEditMode = false;
                this.errors = {};
            },

            edit(jurusan) {
                this.formData.id = jurusan.id;
                this.formData.nama_jurusan = jurusan.nama_jurusan;
                this.formData.singkatan = jurusan.singkatan;
                this.isEditMode = true;
                this.isModalOpen = true;
            },

            async saveJurusan() {
                let url = this.isEditMode ? `/admin/jurusan/${this.formData.id}` : '{{ route("admin.jurusan.store") }}';
                let method = this.isEditMode ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                if (response.ok) {
                    const result = await response.json();
                    this.showSuccessMessage(result.message);
                    this.fetchJurusans();
                    this.closeModal();
                } else if (response.status === 422) {
                    const result = await response.json();
                    this.errors = result;
                } else {
                    console.error('An error occurred');
                }
            },

            openDeleteModal(id) {
                this.jurusanIdToDelete = id;
                this.isDeleteModalOpen = true;
            },

            closeDeleteModal() {
                this.isDeleteModalOpen = false;
                this.jurusanIdToDelete = null;
            },

            async deleteJurusan() {
                if (!this.jurusanIdToDelete) return;

                const response = await fetch(`/admin/jurusan/${this.jurusanIdToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    this.showSuccessMessage(result.message);
                    this.fetchJurusans();
                } else {
                    console.error('Gagal menghapus data');
                }
                
                this.closeDeleteModal();
            },

            showSuccessMessage(message) {
                this.successMessage = message;
                setTimeout(() => {
                    this.successMessage = '';
                }, 3000);
            }
        }
    }
</script>
@endsection
