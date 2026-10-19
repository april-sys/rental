<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display admin overview metrics & urgent booking requests.
     */
    public function index(): View
    {
        $totalCars = Car::count();
        $availableCars = Car::where('status', 'available')->count();
        $rentedCars = Car::where('status', 'rented')->count();
        $maintenanceCars = Car::where('status', 'maintenance')->count();

        $pendingBookings = Booking::where('status', Booking::STATUS_PENDING)->count();
        $activeBookings = Booking::whereIn('status', [
            Booking::STATUS_APPROVED,
            Booking::STATUS_DELIVERING,
            Booking::STATUS_IN_USE,
        ])->count();

        $completedBookings = Booking::where('status', Booking::STATUS_COMPLETED)->count();
        $totalRevenue = Booking::where('status', Booking::STATUS_COMPLETED)->sum('total_price');

        $totalCustomers = User::where('role', 'customer')->count();

        $recentBookings = Booking::with(['user', 'car'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalCars',
            'availableCars',
            'rentedCars',
            'maintenanceCars',
            'pendingBookings',
            'activeBookings',
            'completedBookings',
            'totalRevenue',
            'totalCustomers',
            'recentBookings'
        ));
    }
}
