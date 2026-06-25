<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/ekspor', [ReportExportController::class, 'index'])->name('exports.index');
    Route::get('/ekspor/{type}/{format}', [ReportExportController::class, 'download'])->name('exports.download');

    Route::get('/laporan-harian/export', [DailyReportController::class, 'export'])->name('reports.export');
    Route::get('/laporan-harian', [DailyReportController::class, 'index'])->middleware('permission:production.view')->name('reports.index');
    Route::get('/laporan-harian/tambah', [DailyReportController::class, 'create'])->middleware('permission:production.create')->name('reports.create');
    Route::post('/laporan-harian', [DailyReportController::class, 'store'])->middleware('permission:production.create')->name('reports.store');
    Route::get('/laporan-harian/{report}', [DailyReportController::class, 'show'])->middleware('permission:production.view')->name('reports.show');
    Route::get('/laporan-harian/{report}/edit', [DailyReportController::class, 'edit'])->middleware('permission:production.update')->name('reports.edit');
    Route::put('/laporan-harian/{report}', [DailyReportController::class, 'update'])->middleware('permission:production.update')->name('reports.update');
    Route::delete('/laporan-harian/{report}', [DailyReportController::class, 'destroy'])->middleware('permission:production.delete')->name('reports.destroy');
    Route::post('/laporan-harian/{report}/ajukan', [DailyReportController::class, 'submit'])->name('reports.submit');
    Route::post('/laporan-harian/{report}/validasi', [DailyReportController::class, 'validateReport'])->middleware('permission:production.validate')->name('reports.validate');
    Route::post('/laporan-harian/{report}/tolak', [DailyReportController::class, 'reject'])->middleware('permission:production.validate')->name('reports.reject');
    Route::post('/laporan-harian/{report}/minta-revisi', [DailyReportController::class, 'requestRevision'])->name('reports.request-revision');
    Route::post('/laporan-harian/{report}/buka-revisi', [DailyReportController::class, 'reopen'])->middleware('permission:production.validate')->name('reports.reopen');

    Route::resource('petugas', StaffController::class)->except(['show'])->parameters(['petugas' => 'staff'])->middleware('permission:staff.manage')->names('staff');
    Route::get('/absensi', [AttendanceController::class, 'index'])->middleware('permission:attendance.view')->name('attendance.index');
    Route::post('/absensi/check-in', [AttendanceController::class, 'checkIn'])->middleware('permission:attendance.create')->name('attendance.check-in');
    Route::post('/absensi/check-out', [AttendanceController::class, 'checkOut'])->middleware('permission:attendance.create')->name('attendance.check-out');

    Route::resource('aset', AssetController::class)->parameters(['aset' => 'asset'])->middleware('permission:asset.view')->names('assets');
    Route::patch('/aset/{asset}/kondisi', [AssetController::class, 'updateCondition'])->middleware('permission:asset.update')->name('assets.condition');
    Route::get('/aset/{asset}/perawatan/tambah', [MaintenanceController::class, 'create'])->middleware('permission:maintenance.create')->name('maintenance.create');
    Route::post('/aset/{asset}/perawatan', [MaintenanceController::class, 'store'])->middleware('permission:maintenance.create')->name('maintenance.store');
    Route::get('/aset/{asset}/perawatan/{maintenance}/edit', [MaintenanceController::class, 'edit'])->middleware('permission:maintenance.create')->name('maintenance.edit');
    Route::put('/aset/{asset}/perawatan/{maintenance}', [MaintenanceController::class, 'update'])->middleware('permission:maintenance.create')->name('maintenance.update');

    Route::get('/pengguna', [UserManagementController::class, 'index'])->middleware('permission:user.view')->name('users.index');
    Route::post('/pengguna', [UserManagementController::class, 'store'])->middleware('permission:user.create')->name('users.store');
    Route::put('/pengguna/{user}', [UserManagementController::class, 'update'])->middleware('permission:user.update')->name('users.update');
    Route::delete('/pengguna/{user}', [UserManagementController::class, 'destroy'])->middleware('permission:user.delete')->name('users.destroy');
    Route::get('/rbac', [UserManagementController::class, 'matrix'])->middleware('permission:role.manage')->name('rbac.index');
    Route::get('/lokasi', [LocationController::class, 'index'])->middleware('permission:location.manage')->name('locations.index');
    Route::post('/lokasi', [LocationController::class, 'store'])->middleware('permission:location.manage')->name('locations.store');
    Route::put('/lokasi/{location}', [LocationController::class, 'update'])->middleware('permission:location.manage')->name('locations.update');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/notifikasi/{notification}', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store'])->name('push.store');
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy'])->name('push.destroy');
});
