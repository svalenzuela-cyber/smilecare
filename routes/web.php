<?php

use App\Http\Controllers\AdminAppointmentController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DentistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Models\Dentist;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

// PUBLIC landing page: shows active services and dentists from the database
Route::get('/', function () {
    return view('welcome', [
        'services' => Service::where('is_active', true)->orderBy('name')->get(),
        'dentists' => Dentist::where('is_active', true)->orderBy('name')->get(),
    ]);
});

// Dashboard: admin analytics or patient summary, depending on the role
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// PATIENT ONLY: book and manage their own appointments
Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/book', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

// ADMIN ONLY: every page under /admin requires a logged-in admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('services', ServiceController::class)->except(['show']);
    Route::resource('dentists', DentistController::class)->except(['show']);
    Route::get('appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::patch('appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::get('reports/appointments', [DashboardController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';