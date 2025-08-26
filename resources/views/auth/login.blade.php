<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $sekolahSetting->nama_sekolah ?? 'Aplikasi Presensi' }}</title>

    <!-- Favicon Dinamis -->
    @if(isset($sekolahSetting) && $sekolahSetting->favicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $sekolahSetting->favicon) }}">
    @endif

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .bg-gradient-custom {
            background-image: linear-gradient(to top right, #4f46e5, #818cf8);
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- Kolom Kiri (Branding/Gambar) -->
        <div class="hidden lg:flex w-1/2 items-center justify-center bg-gradient-custom p-12 text-white relative overflow-hidden">
            <div class="z-10 text-center">
                <h1 class="text-4xl font-bold tracking-tight">Selamat Datang Kembali</h1>
                <p class="mt-4 text-lg opacity-80">Aplikasi Presensi dan Rekap Nilai Siswa. Silakan masuk untuk melanjutkan.</p>
            </div>
            <!-- Elemen dekoratif -->
            <div class="absolute top-0 left-0 w-48 h-48 bg-white opacity-10 rounded-full -translate-x-1/3 -translate-y-1/3"></div>
            <div class="absolute bottom-0 right-0 w-72 h-72 bg-white opacity-10 rounded-full translate-x-1/4 translate-y-1/4"></div>
        </div>

        <!-- Kolom Kanan (Form Login) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12">
            <div class="w-full max-w-md">
                <div class="text-center lg:text-left mb-10">
                    <h2 class="text-3xl font-bold text-gray-800">Login Akun</h2>
                    <p class="mt-2 text-gray-500">Masukkan email dan password Anda.</p>
                </div>

                <!-- FORM LOGIN -->
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <!-- Menampilkan Error Validasi -->
                    @error('email')
                        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded-md" role="alert">
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="space-y-6">
                        <!-- Input Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                            <div class="mt-1">
                                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                    class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition duration-150 ease-in-out"
                                    placeholder="contoh@email.com">
                            </div>
                        </div>

                        <!-- Input Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="mt-1">
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                    class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition duration-150 ease-in-out"
                                    placeholder="********">
                            </div>
                        </div>

                        <!-- Opsi Tambahan -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember-me" name="remember-me" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="remember-me" class="ml-2 block text-sm text-gray-900">Ingat saya</label>
                            </div>
                            <div class="text-sm">
                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Lupa password?</a>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div>
                            <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                Masuk
                            </button>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

</body>
</html>
