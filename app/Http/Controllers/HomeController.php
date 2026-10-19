<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show customer landing page with car exploration & filtering.
     */
    public function index(Request $request): View
    {
        $query = Car::query();

        // Search by brand or model
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filter by capacity
        if ($capacity = $request->input('capacity')) {
            $query->where('capacity', '>=', (int) $capacity);
        }

        // Filter by maximum price
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price_per_day', '<=', (float) $maxPrice);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $cars = $query->orderBy('price_per_day', 'asc')->paginate(9)->withQueryString();

        $brands = Car::select('brand')->distinct()->pluck('brand');

        $stats = [
            'total_cars' => Car::count(),
            'available_cars' => Car::where('status', 'available')->count(),
            'rented_cars' => Car::where('status', 'rented')->count(),
        ];

        return view('home', compact('cars', 'brands', 'stats'));
    }

    /**
     * Show car detail.
     */
    public function showCar(Car $car): View
    {
        // Upcoming active bookings to display calendar busy slots
        $activeBookings = $car->bookings()
            ->whereIn('status', ['approved', 'delivering', 'in_use'])
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->get(['start_date', 'end_date']);

        return view('cars.show', compact('car', 'activeBookings'));
    }
}
