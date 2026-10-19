<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarRequest;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CarController extends Controller
{
    /**
     * Display a listing of cars.
     */
    public function index(Request $request): View
    {
        $query = Car::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $cars = $query->latest()->paginate(10)->withQueryString();

        return view('admin.cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new car.
     */
    public function create(): View
    {
        return view('admin.cars.create');
    }

    /**
     * Store a newly created car in storage.
     */
    public function store(CarRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
            $validated['image'] = $path;
        } else {
            $validated['image'] = null; // Automatically falls back to public/images/default-car.png
        }

        $car = Car::create($validated);

        return redirect()->route('admin.cars.index')
            ->with('success', "Mobil {$car->brand} {$car->model} ({$car->license_plate}) berhasil ditambahkan ke armada.");
    }

    /**
     * Show the form for editing the specified car.
     */
    public function edit(Car $car): View
    {
        return view('admin.cars.edit', compact('car'));
    }

    /**
     * Update the specified car in storage.
     */
    public function update(CarRequest $request, Car $car): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old file if stored locally in public disk
            if ($car->image && !str_starts_with($car->image, 'http') && Storage::disk('public')->exists($car->image)) {
                Storage::disk('public')->delete($car->image);
            }

            $path = $request->file('image')->store('cars', 'public');
            $validated['image'] = $path;
        } else {
            unset($validated['image']);
        }

        $car->update($validated);

        return redirect()->route('admin.cars.index')
            ->with('success', "Data mobil {$car->brand} {$car->model} berhasil diperbarui.");
    }

    /**
     * Remove the specified car from storage.
     */
    public function destroy(Car $car): RedirectResponse
    {
        // Check for active ongoing bookings
        $hasActiveBookings = $car->bookings()
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_APPROVED,
                Booking::STATUS_DELIVERING,
                Booking::STATUS_IN_USE,
            ])
            ->exists();

        if ($hasActiveBookings) {
            return back()->with('error', "Mobil tidak dapat dihapus karena masih terikat dalam transaksi pemesanan yang sedang berjalan.");
        }

        // Clean up stored image file if exists
        if ($car->image && !str_starts_with($car->image, 'http') && Storage::disk('public')->exists($car->image)) {
            Storage::disk('public')->delete($car->image);
        }

        $plate = $car->license_plate;
        $name = $car->full_name;
        $car->delete();

        return redirect()->route('admin.cars.index')
            ->with('success', "Mobil {$name} ({$plate}) berhasil dihapus dari sistem.");
    }
}

