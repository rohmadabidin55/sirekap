@extends('layouts.admin')

@section('title', 'Manajemen Siswa')

@section('content')
<div x-data="siswaCrud()">
    <div class="flex flex-col sm:flex-row justify-between items-center">
        <h3 class="text-gray-700 text-3xl font-medium">Data Siswa</h3>
        <div class="flex space-x-2 mt-4 sm:mt-0">
            <button @click="isImportModalOpen = true" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Impor Siswa
            </button>
            <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Tambah Siswa
            </button>
        </div>
    </div>
    
    <div x-show="successMessage" x-text="successMessage" x-transition class="mt-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-md"></div>
    <div x-show="errorMessage" x-text="errorMessage" x-transition class="mt-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded-md"></div>

    <!-- Form Filter dan Pagination (dengan Live Search) -->
    <div class="mt-6 bg-white p-4 rounded-md shadow-sm border border-gray-200">
        <form x-data action="{{ route('admin.siswa.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-1">
                    <label for="search" class="text-sm">Cari Nama Siswa</label>
                    <input type="text" name="search" id="search" placeholder="Ketik nama untuk mencari..." value="{{ request('search') }}" 
                           @input.debounce.500ms="$el.form.submit()"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="kelas_filter" class="text-sm">Filter Kelas</label>
                    <select name="kelas_filter" id="kelas_filter" @change="$el.form.submit()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_filter') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
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

    <!-- Tabel Data Siswa -->
    <div class="flex flex-col mt-6">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($siswas as $siswa)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                     <img class="h-10 w-10 rounded-full object-cover" src="{{ $siswa->user->photo ? asset('storage/' . $siswa->user->photo) : 'https://placehold.co/100x100/E2E8F0/4A5568?text=S' }}" alt="Foto Siswa">
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $siswa->nis }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $siswa->user->name }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $siswa->kelas->nama_kelas }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium">
                                    <div class="flex justify-center items-center space-x-4">
                                        <button @click="edit({{ $siswa }})" class="text-gray-500 hover:text-indigo-600" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <button @click="openDeleteModal({{ $siswa->id }})" class="text-gray-500 hover:text-red-600" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data siswa yang cocok.</td>
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

    <!-- Modal Form Tambah/Edit -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="closeModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative flex flex-col w-full max-w-lg bg-white rounded-2xl shadow-xl" style="max-height: 90vh;">
            <div class="flex-shrink-0 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="isEditMode ? 'Edit Siswa' : 'Tambah Siswa'"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="flex-grow p-6 overflow-y-auto">
                <form id="siswaForm" @submit.prevent="saveSiswa()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2"><label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label><input type="text" x-model="formData.name" id="name" class="mt-1 block w-full rounded-md" required></div>
                        <div class="md:col-span-2"><label for="email" class="block text-sm font-medium text-gray-700">Email</label><input type="email" x-model="formData.email" id="email" class="mt-1 block w-full rounded-md" required></div>
                        <div class="md:col-span-2"><label for="password" class="block text-sm font-medium text-gray-700">Password</label><input type="password" x-model="formData.password" id="password" class="mt-1 block w-full rounded-md" :required="!isEditMode"><small class="text-gray-500" x-show="isEditMode">Kosongkan jika tidak ingin mengubah password.</small></div>
                        <div><label for="nis" class="block text-sm font-medium text-gray-700">NIS</label><input type="text" x-model="formData.nis" id="nis" class="mt-1 block w-full rounded-md" required></div>
                        <div><label for="nisn" class="block text-sm font-medium text-gray-700">NISN</label><input type="text" x-model="formData.nisn" id="nisn" class="mt-1 block w-full rounded-md"></div>
                        <div class="md:col-span-2"><label for="kelas_id" class="block text-sm font-medium text-gray-700">Kelas</label><select x-model="formData.kelas_id" id="kelas_id" class="mt-1 block w-full rounded-md" required><option value="">Pilih Kelas</option>@foreach($kelas as $k)<option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>@endforeach</select></div>
                        <div class="md:col-span-2"><label for="no_telepon_orang_tua" class="block text-sm font-medium text-gray-700">No. Telp Ortu</label><input type="text" x-model="formData.no_telepon_orang_tua" id="no_telepon_orang_tua" class="mt-1 block w-full rounded-md"></div>
                        <div class="md:col-span-2"><label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label><textarea x-model="formData.alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-md"></textarea></div>
                        <div class="md:col-span-2"><label for="photo_siswa" class="block text-sm font-medium text-gray-700">Foto</label><input type="file" id="photo_siswa" @change="handleFileSelect" class="mt-1 block w-full text-sm"><template x-if="photoPreview"><img :src="photoPreview" class="mt-4 h-24 w-24 rounded-full object-cover"></template></div>
                    </div>
                </form>
            </div>
            <div class="flex-shrink-0 bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t">
                <button type="button" @click="closeModal()" class="rounded-md border bg-white py-2 px-4 text-sm">Batal</button>
                <button type="submit" form="siswaForm" class="rounded-md border bg-indigo-600 py-2 px-4 text-sm text-white">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Modal Import -->
    <div x-show="isImportModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="isImportModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl">
            <form @submit.prevent="saveImport()">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Impor Data Siswa</h3>
                    <div class="mt-4">
                        <label for="import_file" class="block text-sm font-medium text-gray-700">Pilih File Excel</label>
                        <input type="file" id="import_file" @change="importFile = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                        <a href="{{ route('admin.siswa.template') }}" class="text-xs text-blue-600 hover:underline mt-1">Unduh Template Excel</a>
                    </div>
                    <ul x-show="importErrors.length > 0" class="mt-4 list-disc list-inside text-sm text-red-600 bg-red-50 p-3 rounded-md">
                        <template x-for="error in importErrors" :key="error">
                            <li x-text="error"></li>
                        </template>
                    </ul>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button type="button" @click="isImportModalOpen = false" class="rounded-md border bg-white py-2 px-4 text-sm">Batal</button>
                    <button type="submit" class="rounded-md border bg-green-600 py-2 px-4 text-sm text-white">Impor</button>
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
                <h3 class="mt-2 text-lg font-medium text-gray-900">Hapus Data Siswa</h3>
                <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin? Data user yang terkait juga akan dihapus.</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-center space-x-3">
                <button @click="closeDeleteModal()" type="button" class="rounded-md border bg-white py-2 px-4 text-sm">Batal</button>
                <button @click="deleteSiswa()" type="button" class="rounded-md border bg-red-600 py-2 px-4 text-sm text-white">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    function siswaCrud() {
        return {
            isModalOpen: false,
            isImportModalOpen: false,
            isDeleteModalOpen: false,
            siswaIdToDelete: null,
            isEditMode: false,
            successMessage: '',
            errorMessage: '',
            formData: {},
            errors: {},
            photoPreview: null,
            photoFile: null,
            importFile: null,
            importErrors: [],
            
            init() { this.resetForm(); },
            handleFileSelect(event) { if (event.target.files.length) { this.photoFile = event.target.files[0]; this.photoPreview = URL.createObjectURL(this.photoFile); } },
            openModal() { this.resetForm(); this.isModalOpen = true; },
            closeModal() { this.isModalOpen = false; document.getElementById('photo_siswa').value = ''; },
            resetForm() {
                this.formData = { id: null, name: '', email: '', password: '', nis: '', nisn: '', kelas_id: '', alamat: '', no_telepon_orang_tua: '' };
                this.isEditMode = false; this.errors = {}; this.photoPreview = null; this.photoFile = null;
            },
            edit(siswa) {
                this.resetForm();
                this.formData = {
                    id: siswa.id, name: siswa.user.name, email: siswa.user.email, password: '',
                    nis: siswa.nis, nisn: siswa.nisn, kelas_id: siswa.kelas_id,
                    alamat: siswa.alamat || '', no_telepon_orang_tua: siswa.no_telepon_orang_tua || ''
                };
                if (siswa.user.photo) { this.photoPreview = '/storage/' + siswa.user.photo; }
                this.isEditMode = true; this.isModalOpen = true;
            },
            async saveSiswa() {
                let formPayload = new FormData();
                for (const key in this.formData) { if (this.formData[key] !== null) { formPayload.append(key, this.formData[key]); } }
                if (this.photoFile) { formPayload.append('photo', this.photoFile); }

                let url = '{{ route("admin.siswa.store") }}';
                if (this.isEditMode) { url = `/admin/siswa/${this.formData.id}`; formPayload.append('_method', 'PUT'); }

                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formPayload
                });

                if (response.ok) { window.location.reload(); } 
                else if (response.status === 422) { this.errors = await response.json(); } 
                else { console.error('An error occurred'); }
            },
            async saveImport() {
                this.importErrors = [];
                let formPayload = new FormData();
                formPayload.append('file', this.importFile);

                const response = await fetch('{{ route("admin.siswa.import") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formPayload
                });
                
                const result = await response.json();
                if (response.ok) {
                    window.location.reload();
                } else if (response.status === 422) {
                    this.importErrors = result.errors;
                } else {
                    this.errorMessage = result.message || 'Terjadi kesalahan saat impor.';
                }
            },
            openDeleteModal(id) { this.siswaIdToDelete = id; this.isDeleteModalOpen = true; },
            closeDeleteModal() { this.isDeleteModalOpen = false; this.siswaIdToDelete = null; },
            async deleteSiswa() {
                if (!this.siswaIdToDelete) return;
                const response = await fetch(`/admin/siswa/${this.siswaIdToDelete}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                if(response.ok) { window.location.reload(); }
            },
        }
    }
</script>
@endsection
