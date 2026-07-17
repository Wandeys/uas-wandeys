<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DosenKelasController;
use App\Http\Controllers\KhsController;
use App\Http\Controllers\GradeReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('login.logout');
    Route::post('/switch-user', [LoginController::class, 'switchUser'])->name('login.switch_user');

    // Route umum (bisa diakses semua role yang login)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/show', [DashboardController::class, 'show'])->name('dashboard.show');
    Route::get('/dashboard/edit', [DashboardController::class, 'edit'])->name('dashboard.edit');
    Route::put('/dashboard/update', [DashboardController::class, 'update'])->name('dashboard.update');
    Route::get('/kelas/{class}/download-pdf', [GradeReportController::class, 'downloadClassPdf'])->name('kelas.download_pdf');

    // Khusus Dosen (Didaftarkan sebelum resource /dosen agar rute spesifik tidak bertabrakan dengan wildcard {dosen})
    Route::middleware(['role:Dosen'])->group(function () {
        Route::get('/dosen/kelas', [DosenKelasController::class, 'index'])->name('dosen.kelas.index');
        Route::get('/dosen/kelas/{class}/input-nilai', [DosenKelasController::class, 'inputNilai'])->name('dosen.kelas.input_nilai');
        Route::post('/dosen/kelas/{class}/simpan-nilai', [DosenKelasController::class, 'simpanNilai'])->name('dosen.kelas.simpan_nilai');
        Route::post('/dosen/kelas/{class}/lock-nilai', [DosenKelasController::class, 'lockNilai'])->name('dosen.kelas.lock_nilai');
    });

    // Khusus Mahasiswa (Didaftarkan sebelum resource /mahasiswa agar rute spesifik tidak bertabrakan dengan wildcard {mahasiswa})
    Route::middleware(['role:Mahasiswa'])->group(function () {
        Route::get('/khs', [KhsController::class, 'index'])->name('khs.index');
        Route::get('/khs/cetak', [KhsController::class, 'cetak'])->name('khs.cetak');
    });

    // Khusus Superadmin & Admin
    Route::middleware(['role:Superadmin,Admin'])->group(function () {
        Route::resource('/user', UserController::class);
        Route::resource('/dosen', DosenController::class);
        Route::resource('/mahasiswa', MahasiswaController::class);
        Route::resource('/matakuliah', CourseController::class);
        Route::resource('/tahun-akademik', AcademicYearController::class);
        Route::resource('/kelas', ClassController::class);
        
        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::put('/setting/{setting}/update', [SettingController::class, 'update'])->name('setting.update');
    });
});
