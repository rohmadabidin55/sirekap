<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Guru') - Aplikasi Presensi</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- PERBAIKAN: Tambahkan script plugin datalabels -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 antialiased" x-data="profileHandler()">
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Header Sederhana dengan Tombol Logout -->
        <header class="flex justify-between items-center">
            <a href="{{ route('guru.dashboard') }}" class="text-xl font-bold text-gray-800 hover:text-indigo-600 transition">Dashboard Guru</a>
            <div class="flex items-center space-x-4">
                <button @click="openProfileModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Edit Profil
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Konten Utama -->
        <main class="mt-6">
            @yield('content')
        </main>
    </div>

    <!-- Modal Edit Profil -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="closeProfileModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative flex flex-col w-full max-w-2xl bg-white rounded-2xl shadow-xl" style="max-height: 90vh;">
            <div class="flex-shrink-0 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Profil</h3>
                <button @click="closeProfileModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="flex-grow p-6 overflow-y-auto">
                <div x-show="successMessage" x-text="successMessage" class="mb-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-lg"></div>
                <form id="profileForm" @submit.prevent="saveProfile()">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="md:col-span-1 flex flex-col items-center">
                            <img :src="photoPreview" class="h-40 w-40 rounded-full object-cover ring-4 ring-indigo-100">
                            <label for="photo" class="mt-4 cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Ganti Foto</label>
                            <input type="file" id="photo" @change="handleFileSelect" class="hidden">
                        </div>
                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" x-model="formData.name" id="name" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                                <input type="email" x-model="formData.email" id="email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input type="text" x-model="formData.no_telepon" id="no_telepon" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea x-model="formData.alamat" id="alamat" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"></textarea>
                            </div>
                            <hr>
                            <h3 class="text-md font-medium text-gray-800">Ubah Password (Opsional)</h3>
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                                <input type="password" x-model="formData.current_password" id="current_password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                <input type="password" x-model="formData.new_password" id="new_password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            </div>
                             <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                <input type="password" x-model="formData.new_password_confirmation" id="new_password_confirmation" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="flex-shrink-0 bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200">
                <button type="button" @click="closeProfileModal()" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Batal</button>
                <button type="submit" form="profileForm" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function profileHandler() {
            return {
                isModalOpen: false,
                successMessage: '',
                formData: {},
                photoPreview: 'https://placehold.co/200x200/E2E8F0/4A5568?text=G',
                photoFile: null,

                async openProfileModal() {
                    const response = await fetch('{{ route("guru.profile.show") }}');
                    const user = await response.json();
                    
                    this.formData = {
                        name: user.name,
                        email: user.email,
                        no_telepon: user.guru.no_telepon,
                        alamat: user.guru.alamat,
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: '',
                    };
                    this.photoPreview = user.photo ? `/storage/${user.photo}` : 'https://placehold.co/200x200/E2E8F0/4A5568?text=G';
                    this.isModalOpen = true;
                },

                closeProfileModal() {
                    this.isModalOpen = false;
                    this.successMessage = '';
                    this.photoFile = null;
                },

                handleFileSelect(event) {
                    if (event.target.files.length) {
                        this.photoFile = event.target.files[0];
                        this.photoPreview = URL.createObjectURL(this.photoFile);
                    }
                },

                async saveProfile() {
                    let formPayload = new FormData();
                    for (const key in this.formData) {
                        if (this.formData[key]) {
                            formPayload.append(key, this.formData[key]);
                        }
                    }
                    if (this.photoFile) {
                        formPayload.append('photo', this.photoFile);
                    }

                    const response = await fetch('{{ route("guru.profile.update") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formPayload
                    });

                    const result = await response.json();

                    if (response.ok) {
                        this.successMessage = result.message;
                        setTimeout(() => this.closeProfileModal(), 2000);
                    } else if (response.status === 422) {
                        alert('Error: ' + Object.values(result.errors).flat().join('\n'));
                    }
                }
            }
        }
    </script>
</body>
</html>
