@extends('layouts.admin')

@section('title', 'Manajemen Guru')

@section('content')
<div x-data="guruCrud()">
    <div class="flex flex-col sm:flex-row justify-between items-center">
        <h3 class="text-gray-700 text-3xl font-medium">Data Guru</h3>
        <button @click="openModal()" class="mt-4 sm:mt-0 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
            Tambah Guru
        </button>
    </div>
    
    <div x-show="successMessage" x-text="successMessage" x-transition class="mt-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-md"></div>

    <!-- Form Filter dan Pagination (dengan Live Search) -->
    <div class="mt-6 bg-white p-4 rounded-md shadow-sm border border-gray-200">
        <form x-data action="{{ route('admin.guru.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    <label for="search" class="text-sm">Cari Nama Guru</label>
                    <input type="text" name="search" id="search" placeholder="Ketik nama untuk mencari..." value="{{ request('search') }}" 
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

    <!-- Tabel Data Guru -->
    <div class="flex flex-col mt-6">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nama Guru</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($gurus as $guru)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $guru->user->photo ? asset('storage/' . $guru->user->photo) : 'https://placehold.co/100x100/E2E8F0/4A5568?text=G' }}" alt="Foto Guru">
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $guru->nip ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $guru->user->name }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $guru->user->email }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium">
                                    <div class="flex justify-center items-center space-x-4">
                                        <button @click="edit({{ $guru }})" class="text-gray-500 hover:text-indigo-600" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <button @click="openDeleteModal({{ $guru->id }})" class="text-gray-500 hover:text-red-600" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data guru yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $gurus->links() }}
    </div>

    <!-- Modal Form -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="closeModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative flex flex-col w-full max-w-lg bg-white rounded-2xl shadow-xl" style="max-height: 90vh;">
            <div class="flex-shrink-0 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="isEditMode ? 'Edit Guru' : 'Tambah Guru'"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="flex-grow p-6 overflow-y-auto">
                <form id="guruForm" @submit.prevent="saveGuru()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" x-model="formData.name" id="name" class="mt-1 block w-full rounded-md" required>
                            <span x-show="errors.name" x-text="errors.name ? errors.name[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" x-model="formData.email" id="email" class="mt-1 block w-full rounded-md" required>
                            <span x-show="errors.email" x-text="errors.email ? errors.email[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                        <div class="md:col-span-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" x-model="formData.password" id="password" class="mt-1 block w-full rounded-md" :required="!isEditMode">
                            <small class="text-gray-500" x-show="isEditMode">Kosongkan jika tidak ingin mengubah password.</small>
                            <span x-show="errors.password" x-text="errors.password ? errors.password[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                        <div class="md:col-span-2">
                            <label for="nip" class="block text-sm font-medium text-gray-700">NIP (Opsional)</label>
                            <input type="text" x-model="formData.nip" id="nip" class="mt-1 block w-full rounded-md">
                            <span x-show="errors.nip" x-text="errors.nip ? errors.nip[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                        <div class="md:col-span-2">
                            <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                            <input type="text" x-model="formData.no_telepon" id="no_telepon" class="mt-1 block w-full rounded-md">
                        </div>
                        <div class="md:col-span-2">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea x-model="formData.alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-md"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="photo" class="block text-sm font-medium text-gray-700">Foto</label>
                            <input type="file" id="photo" @change="handleFileSelect" class="mt-1 block w-full text-sm">
                            <template x-if="photoPreview"><img :src="photoPreview" class="mt-4 h-24 w-24 rounded-full object-cover"></template>
                            <span x-show="errors.photo" x-text="errors.photo ? errors.photo[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="flex-shrink-0 bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t">
                <button type="button" @click="closeModal()" class="rounded-md border bg-white py-2 px-4 text-sm">Batal</button>
                <button type="submit" form="guruForm" class="rounded-md border bg-indigo-600 py-2 px-4 text-sm text-white">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="isDeleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="closeDeleteModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all">
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Hapus Data Guru</h3>
                <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin? Data user yang terkait juga akan dihapus.</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-center space-x-3">
                <button @click="closeDeleteModal()" type="button" class="rounded-md border bg-white py-2 px-4 text-sm">Batal</button>
                <button @click="deleteGuru()" type="button" class="rounded-md border bg-red-600 py-2 px-4 text-sm text-white">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    function guruCrud() {
        return {
            isModalOpen: false,
            isDeleteModalOpen: false,
            guruIdToDelete: null,
            isEditMode: false,
            successMessage: '',
            formData: {},
            errors: {},
            photoPreview: null,
            photoFile: null,
            
            init() { this.resetForm(); },
            handleFileSelect(event) { if (event.target.files.length) { this.photoFile = event.target.files[0]; this.photoPreview = URL.createObjectURL(this.photoFile); } },
            openModal() { this.resetForm(); this.isModalOpen = true; },
            closeModal() { this.isModalOpen = false; document.getElementById('photo').value = ''; },
            resetForm() {
                this.formData = { id: null, name: '', email: '', password: '', nip: '', alamat: '', no_telepon: '' };
                this.isEditMode = false; this.errors = {}; this.photoPreview = null; this.photoFile = null;
            },
            edit(guru) {
                this.resetForm();
                this.formData = {
                    id: guru.id, name: guru.user.name, email: guru.user.email, password: '',
                    nip: guru.nip || '', alamat: guru.alamat || '', no_telepon: guru.no_telepon || ''
                };
                if (guru.user.photo) { this.photoPreview = '/storage/' + guru.user.photo; }
                this.isEditMode = true; this.isModalOpen = true;
            },
            async saveGuru() {
                this.errors = {}; // Kosongkan error sebelum submit
                let formPayload = new FormData();
                for (const key in this.formData) { if (this.formData[key] !== null) { formPayload.append(key, this.formData[key]); } }
                if (this.photoFile) { formPayload.append('photo', this.photoFile); }

                let url = '{{ route("admin.guru.store") }}';
                if (this.isEditMode) { url = `/admin/guru/${this.formData.id}`; formPayload.append('_method', 'PUT'); }

                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formPayload
                });

                if (response.ok) {
                    window.location.reload();
                } else if (response.status === 422) {
                    const result = await response.json();
                    this.errors = result.errors;
                } else { 
                    console.error('An error occurred');
                    alert('Terjadi kesalahan pada server.');
                }
            },
            openDeleteModal(id) { this.guruIdToDelete = id; this.isDeleteModalOpen = true; },
            closeDeleteModal() { this.isDeleteModalOpen = false; this.guruIdToDelete = null; },
            async deleteGuru() {
                if (!this.guruIdToDelete) return;
                const response = await fetch(`/admin/guru/${this.guruIdToDelete}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                if(response.ok) { window.location.reload(); }
            },
        }
    }
</script>
@endsection
