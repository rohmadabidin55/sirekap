<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\SekolahController;
use App\Http\Controllers\Admin\GuruAsuhController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Guru\RekapMapelController;
use App\Http\Controllers\Admin\RekapLaporanController;
use App\Http\Controllers\Admin\RekapPerGuruController;
use App\Http\Controllers\Guru\RekapAnakAsuhController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\GuruMataPelajaranController;
use App\Http\Controllers\Guru\ProfileController as GuruProfileController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route Halaman Utama sekarang mengarah ke halaman login
Route::get('/', [LoginController::class, 'showLoginForm']);

// Route untuk Autentikasi
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


// Route Group untuk Admin
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('jurusan', JurusanController::class)->except(['create', 'edit', 'show']);
    Route::resource('user', UserController::class)->except(['create', 'edit', 'show']);
    
    // Grup Route Siswa
    Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/template', [SiswaController::class, 'exportTemplate'])->name('siswa.template'); // Route Template
    Route::resource('siswa', SiswaController::class)->except(['create', 'edit', 'show']);

    Route::resource('guru', GuruController::class)->except(['create', 'edit', 'show']);
    Route::resource('kelas', KelasController::class, ['parameters' => ['kelas' => 'kela']])->except(['create', 'edit', 'show']);
    Route::resource('matapelajaran', MataPelajaranController::class, ['parameters' => ['matapelajaran' => 'mataPelajaran']])->except(['create', 'edit', 'show']);
    Route::resource('gurumatapelajaran', GuruMataPelajaranController::class, ['parameters' => ['gurumatapelajaran' => 'guruMataPelajaran']])->only(['index', 'store', 'destroy']);
    Route::resource('guruasuh', GuruAsuhController::class, ['parameters' => ['guruasuh' => 'guruAsuh']])->only(['index', 'store', 'destroy']);
    
    // Route untuk Data Sekolah
    Route::get('/sekolah', [SekolahController::class, 'index'])->name('sekolah.index');
    Route::put('/sekolah', [SekolahController::class, 'update'])->name('sekolah.update');

    // Route untuk Rekap Laporan
    Route::get('/rekap', [RekapLaporanController::class, 'index'])->name('rekap.laporan.index');
    Route::get('/rekap-per-guru', [RekapPerGuruController::class, 'index'])->name('rekap.perguru.index');

    // Route untuk Profil Admin
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
});


// Route Group untuk Guru
Route::middleware(['auth'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
    Route::get('/get-siswa', [GuruDashboardController::class, 'getSiswa'])->name('getSiswa');
    Route::post('/update-nilai-kehadiran', [GuruDashboardController::class, 'updateNilaiDanKehadiran'])->name('updateNilaiDanKehadiran');
    
    // Route untuk Profil
    Route::get('/profile', [GuruProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [GuruProfileController::class, 'update'])->name('profile.update');

    // Route untuk Rekap
    Route::get('/rekap-mapel', [RekapMapelController::class, 'index'])->name('rekap.mapel.index');
    Route::get('/rekap-anak-asuh', [RekapAnakAsuhController::class, 'index'])->name('rekap.anakasuh.index');
});

// Route Group untuk Siswa
Route::middleware(['auth'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
});
