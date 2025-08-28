<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin') - {{ $sekolahSetting->nama_sekolah ?? 'Aplikasi Presensi' }}</title>
    
    <!-- Favicon Dinamis -->
    @if(isset($sekolahSetting) && $sekolahSetting->favicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $sekolahSetting->favicon) }}">
    @endif

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans" x-data="profileHandler()">
    <div x-data="{ isSidebarOpen: window.innerWidth >= 1024 }" @resize.window="isSidebarOpen = window.innerWidth >= 1024">
        <!-- Sidebar -->
        <aside 
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed z-40 inset-y-0 left-0 w-64 transition-transform duration-300 transform bg-gray-900 overflow-y-auto">
            
            <div class="flex items-center justify-center mt-8">
                <div class="flex items-center">
                    @if(isset($sekolahSetting) && $sekolahSetting->logo)
                        <img class="h-10 w-10 mr-2 rounded-md object-contain" src="{{ asset('storage/' . $sekolahSetting->logo) }}" alt="Logo Sekolah">
                    @endif
                    <span class="text-white text-2xl font-semibold">Sirekap App</span>
                </div>
            </div>
            
            <nav class="mt-10" x-data="{ open: '' }">
                <a class="flex items-center mt-4 py-2 px-6 text-gray-100 {{ request()->is('admin/dashboard*') ? 'bg-gray-700 bg-opacity-25' : 'text-gray-400 hover:bg-gray-700 hover:bg-opacity-25' }}" href="{{ route('admin.dashboard') }}">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    <span class="mx-3">Dashboard</span>
                </a>

                <!-- Menu Master Data -->
                <div>
                    <button @click="open = open === 'master' ? '' : 'master'" class="w-full flex justify-between items-center mt-4 py-2 px-6 text-gray-400 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 focus:outline-none">
                        <div class="flex items-center">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4M4 7l8 5 8-5" /></svg>
                            <span class="mx-3">Master Data</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'master'}" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open === 'master'" x-cloak class="pl-10 bg-gray-800">
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.jurusan.index') }}">Jurusan</a>
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.kelas.index') }}">Kelas</a>
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.matapelajaran.index') }}">Mata Pelajaran</a>
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.guru.index') }}">Guru</a>
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.siswa.index') }}">Siswa</a>
                    </div>
                </div>

                <!-- Menu Pengaturan -->
                <div>
                    <button @click="open = open === 'pengaturan' ? '' : 'pengaturan'" class="w-full flex justify-between items-center mt-4 py-2 px-6 text-gray-400 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 focus:outline-none">
                        <div class="flex items-center">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span class="mx-3">Pengaturan</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'pengaturan'}" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open === 'pengaturan'" x-cloak class="pl-10 bg-gray-800">
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.sekolah.index') }}">Data Sekolah</a>
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.user.index') }}">User</a>
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.guruasuh.index') }}">Guru Asuh</a>
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.gurumatapelajaran.index') }}">Guru Mata Pelajaran</a>
                    </div>
                </div>
                
                <!-- Menu Nilai -->
                <div>
                    <button @click="open = open === 'nilai' ? '' : 'nilai'" class="w-full flex justify-between items-center mt-4 py-2 px-6 text-gray-400 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 focus:outline-none">
                        <div class="flex items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span class="mx-3">Nilai</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'nilai'}" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open === 'nilai'" x-cloak class="pl-10 bg-gray-800">
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.inputnilai.index') }}">Input Nilai</a>
                    </div>
                </div>

                <!-- Menu Laporan -->
                <div>
                    <button @click="open = open === 'laporan' ? '' : 'laporan'" class="w-full flex justify-between items-center mt-4 py-2 px-6 text-gray-400 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 focus:outline-none">
                        <div class="flex items-center">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span class="mx-3">Laporan</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'laporan'}" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open === 'laporan'" x-cloak class="pl-10 bg-gray-800">
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.rekap.laporan.index') }}">Rekap Laporan</a>
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.rekap.perguru.index') }}">Rekap Laporan Perguru</a>
                    </div>
                </div>

                <!-- Menu Utilitas -->
                <div>
                    <button @click="open = open === 'utilitas' ? '' : 'utilitas'" class="w-full flex justify-between items-center mt-4 py-2 px-6 text-gray-400 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100 focus:outline-none">
                        <div class="flex items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            <span class="mx-3">Utilitas</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'utilitas'}" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open === 'utilitas'" x-cloak class="pl-10 bg-gray-800">
                        <a class="block py-2 px-4 text-sm text-gray-400 hover:bg-gray-700 hover:text-gray-100" href="{{ route('admin.backup.index') }}">Backup & Restore</a>
                    </div>
                </div>
            </nav>
        </aside>
        
        <div x-show="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 z-30 bg-black opacity-50 lg:hidden"></div>

        <!-- Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden transition-all duration-300" :class="{ 'lg:ml-64': isSidebarOpen }">
            <header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-indigo-600">
                <button @click="isSidebarOpen = !isSidebarOpen" class="text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="flex-1"></div>
                
                <div class="flex items-center">
                    <span class="font-semibold mr-4">{{ Auth::user()->name }}</span>
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="relative z-10 block h-8 w-8 rounded-full overflow-hidden shadow focus:outline-none" title="Opsi">
                            <img class="h-full w-full object-cover" src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://placehold.co/100x100/E2E8F0/4A5568?text=A' }}" alt="Avatar">
                        </button>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-20">
                            
                            <button @click="openProfileModal(); dropdownOpen = false" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit Profile
                            </button>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container mx-auto px-6 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Edit Profil (Admin) -->
    <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="closeProfileModal()" class="fixed inset-0 bg-gray-900 bg-opacity-75"></div>
        <div class="relative flex flex-col w-full max-w-lg bg-white rounded-2xl shadow-xl" style="max-height: 90vh;">
            <div class="flex-shrink-0 px-6 py-4 border-b"><h3 class="text-lg font-medium text-gray-900">Edit Profil</h3></div>
            <div class="flex-grow p-6 overflow-y-auto">
                <div x-show="successMessage" x-text="successMessage" class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg"></div>
                <form id="profileForm" @submit.prevent="saveProfile()">
                    <div class="space-y-4">
                        <div class="flex flex-col items-center"><img :src="photoPreview" class="h-24 w-24 rounded-full object-cover ring-4 ring-indigo-100"><label for="photo" class="mt-2 cursor-pointer text-sm text-indigo-600 hover:text-indigo-800">Ganti Foto</label><input type="file" id="photo" @change="handleFileSelect" class="hidden"></div>
                        <div><label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label><input type="text" x-model="formData.name" id="name" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"></div>
                        <div><label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label><input type="email" x-model="formData.email" id="email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"></div>
                        <hr><h3 class="text-md font-medium text-gray-800">Ubah Password (Opsional)</h3>
                        <div><label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label><input type="password" x-model="formData.current_password" id="current_password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"></div>
                        <div><label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label><input type="password" x-model="formData.new_password" id="new_password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"></div>
                        <div><label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label><input type="password" x-model="formData.new_password_confirmation" id="new_password_confirmation" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"></div>
                    </div>
                </form>
            </div>
            <div class="flex-shrink-0 bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t">
                <button type="button" @click="closeProfileModal()" class="rounded-md border bg-white py-2 px-4 text-sm">Batal</button>
                <button type="submit" form="profileForm" class="rounded-md border bg-indigo-600 py-2 px-4 text-sm text-white">Simpan</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function profileHandler() {
            return {
                isModalOpen: false, successMessage: '', formData: {},
                photoPreview: '{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://placehold.co/200x200/E2E8F0/4A5568?text=A' }}',
                photoFile: null,
                async openProfileModal() {
                    const response = await fetch('{{ route("admin.profile.show") }}');
                    const user = await response.json();
                    this.formData = { name: user.name, email: user.email, current_password: '', new_password: '', new_password_confirmation: '' };
                    this.photoPreview = user.photo ? `/storage/${user.photo}` : 'https://placehold.co/200x200/E2E8F0/4A5568?text=A';
                    this.isModalOpen = true;
                },
                closeProfileModal() { this.isModalOpen = false; this.successMessage = ''; this.photoFile = null; },
                handleFileSelect(event) { if (event.target.files.length) { this.photoFile = event.target.files[0]; this.photoPreview = URL.createObjectURL(this.photoFile); } },
                async saveProfile() {
                    let formPayload = new FormData();
                    for (const key in this.formData) { if (this.formData[key]) { formPayload.append(key, this.formData[key]); } }
                    if (this.photoFile) { formPayload.append('photo', this.photoFile); }
                    const response = await fetch('{{ route("admin.profile.update") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: formPayload });
                    const result = await response.json();
                    if (response.ok) {
                        this.successMessage = result.message;
                        setTimeout(() => window.location.reload(), 1500);
                    } else if (response.status === 422) { alert('Error: ' + Object.values(result.errors).flat().join('\n')); }
                }
            }
        }
    </script>
</body>
</html>
