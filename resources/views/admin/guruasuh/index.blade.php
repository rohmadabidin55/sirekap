@extends('layouts.admin')

@section('title', 'Manajemen Guru Asuh')

@section('content')
<div x-data="guruAsuhCrud()">
    <div class="flex flex-col sm:flex-row justify-between items-center">
        <h3 class="text-gray-700 text-3xl font-medium">Penetapan Guru Asuh</h3>
        <button @click="openModal()" class="mt-4 sm:mt-0 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Tetapkan Guru Asuh
        </button>
    </div>
    
    <div x-show="successMessage" x-text="successMessage" x-transition class="mt-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-md"></div>

    <!-- Form Filter dan Pagination (dengan Live Search) -->
    <div class="mt-6 bg-white p-4 rounded-md shadow-sm border border-gray-200">
        <form x-data action="{{ route('admin.guruasuh.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    <label for="search" class="text-sm">Cari Nama Siswa atau Guru</label>
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

    <!-- Tabel Data Siswa Asuh -->
    <div class="flex flex-col mt-6">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Guru Asuh</th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($assignments as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $item->siswa->user->name }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $item->siswa->kelas->nama_kelas }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $item->guru->user->name }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium text-center">
                                    <button @click="openDeleteModal({{ $item->id }})" class="text-gray-500 hover:text-red-600" title="Hapus Penetapan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $assignments->links() }}
    </div>

    <!-- Modal Form -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="closeModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all">
            <form @submit.prevent="saveAssignment()">
                <div class="bg-white px-6 pt-5 pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tetapkan Guru Asuh</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="guru_id" class="block text-sm font-medium text-gray-700">Pilih Guru</label>
                            <select x-model="formData.guru_id" id="guru_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->user->name }}</option>
                                @endforeach
                            </select>
                            <span x-show="errors.guru_id" x-text="errors.guru_id ? errors.guru_id[0] : ''" class="text-red-500 text-xs mt-1"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Siswa</label>
                            <input type="text" x-model="search" placeholder="Cari nama siswa..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <div class="mt-2 h-64 overflow-y-auto border rounded-md p-2">
                                <template x-if="Object.keys(filteredSiswa).length === 0">
                                    <p class="text-center text-gray-500">Tidak ada siswa tersedia atau cocok dengan pencarian.</p>
                                </template>
                                <template x-for="(students, className) in filteredSiswa" :key="className">
                                    <div class="py-2">
                                        <div class="font-semibold text-gray-800 border-b pb-1 mb-2 flex items-center">
                                            <input type="checkbox" @click="toggleSelectClass(students)" :checked="areAllSelected(students)" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-3">
                                            <span x-text="className"></span>
                                        </div>
                                        <div class="pl-6 space-y-1 mt-1">
                                            <template x-for="siswa in students" :key="siswa.id">
                                                <label class="flex items-center p-1 rounded-md hover:bg-gray-50">
                                                    <input type="checkbox" :value="siswa.id" x-model="formData.siswa_ids" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                    <span class="ml-3 text-sm text-gray-700" x-text="siswa.user.name"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <span x-show="errors.siswa_ids" x-text="errors.siswa_ids ? errors.siswa_ids[0] : ''" class="text-red-500 text-xs mt-1"></span>
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
                <h3 class="mt-2 text-lg font-medium text-gray-900">Hapus Penetapan</h3>
                <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin ingin menghapus penetapan guru asuh ini?</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-center space-x-3">
                <button @click="closeDeleteModal()" type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Batal</button>
                <button @click="deleteAssignment()" type="button" class="inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    function guruAsuhCrud() {
        const allSiswaGrouped = {!! $siswas->toJson() !!};
        return {
            assignmentList: [],
            search: '',
            isModalOpen: false,
            isDeleteModalOpen: false,
            assignmentIdToDelete: null,
            successMessage: '',
            formData: {},
            errors: {},
            
            init() { this.resetForm(); },
            
            get filteredSiswa() {
                if (this.search === '') return allSiswaGrouped;
                const filtered = {};
                for (const className in allSiswaGrouped) {
                    const students = allSiswaGrouped[className].filter(s => s.user.name.toLowerCase().includes(this.search.toLowerCase()));
                    if (students.length > 0) {
                        filtered[className] = students;
                    }
                }
                return filtered;
            },
            
            openModal() { this.resetForm(); this.isModalOpen = true; },
            closeModal() { this.isModalOpen = false; },
            resetForm() {
                this.formData = { guru_id: '', siswa_ids: [] };
                this.errors = {};
            },

            toggleSelectClass(studentsInClass) {
                const studentIds = studentsInClass.map(s => s.id);
                if (this.areAllSelected(studentsInClass)) {
                    this.formData.siswa_ids = this.formData.siswa_ids.filter(id => !studentIds.includes(id));
                } else {
                    studentIds.forEach(id => {
                        if (!this.formData.siswa_ids.includes(id)) {
                            this.formData.siswa_ids.push(id);
                        }
                    });
                }
            },

            areAllSelected(studentsInClass) {
                const studentIds = studentsInClass.map(s => s.id);
                return studentIds.every(id => this.formData.siswa_ids.includes(id));
            },

            async saveAssignment() {
                const response = await fetch('{{ route("admin.guruasuh.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(this.formData)
                });

                if (response.ok) {
                    window.location.reload();
                } else if (response.status === 422) {
                    this.errors = await response.json();
                } else { console.error('An error occurred'); }
            },

            openDeleteModal(id) { this.assignmentIdToDelete = id; this.isDeleteModalOpen = true; },
            closeDeleteModal() { this.isDeleteModalOpen = false; this.assignmentIdToDelete = null; },

            async deleteAssignment() {
                if (!this.assignmentIdToDelete) return;
                const response = await fetch(`/admin/guruasuh/${this.assignmentIdToDelete}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                
                if (response.ok) {
                    window.location.reload();
                } else { console.error('Gagal menghapus'); }
            },
        }
    }
</script>
@endsection
