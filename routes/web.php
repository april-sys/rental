<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CarController as AdminCarController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Rental Mobil System
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cars/{car}', [HomeController::class, 'showCar'])->name('cars.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Customer Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/cars/{car}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('customer.bookings');
    Route::get('/my-bookings/{booking}', [BookingController::class, 'show'])->name('customer.bookings.show');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('customer.bookings.cancel');

    // Real-Time Notification Endpoints (Admin & Customer)
    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Admin Protected Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Car Fleet Management (CRUD)
        Route::resource('cars', AdminCarController::class)->except(['show']);

        // Booking Lifecycle & Workflow Actions
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');
        Route::post('/bookings/{booking}/deliver', [AdminBookingController::class, 'deliver'])->name('bookings.deliver');
        Route::post('/bookings/{booking}/inUse', [AdminBookingController::class, 'inUse'])->name('bookings.in-use');
        Route::post('/bookings/{booking}/return', [AdminBookingController::class, 'returnCar'])->name('bookings.return');
        Route::post('/bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');

        // Laporan & Rekapitulasi (Reports & Analytics)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
        Route::get('/reports/transactions/export-excel', [ReportController::class, 'exportTransactionsExcel'])->name('reports.transactions.excel');
        Route::get('/reports/transactions/print-pdf', [ReportController::class, 'printTransactionsPdf'])->name('reports.transactions.pdf');
        Route::get('/reports/vehicles', [ReportController::class, 'vehicles'])->name('reports.vehicles');
        Route::get('/reports/vehicles/export-excel', [ReportController::class, 'exportVehiclesExcel'])->name('reports.vehicles.excel');
        Route::get('/reports/vehicles/print-pdf', [ReportController::class, 'printVehiclesPdf'])->name('reports.vehicles.pdf');
    });

