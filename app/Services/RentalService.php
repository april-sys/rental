<?php

namespace App\Services;

use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RentalService
{
    public const DRIVER_DAILY_RATE = 150000; // Rp 150.000 / hari

    /**
     * Calculate duration and breakdown fees for car rental.
     *
     * @return array{
     *     duration_days: int,
     *     car_rental_fee: float,
     *     driver_fee: float,
     *     total_price: float
     * }
     */
    public function calculateFees(Car $car, string $startDate, string $endDate, bool $driverService): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        $diffDays = $start->diffInDays($end);
        $durationDays = max(1, (int) $diffDays);

        $carRentalFee = (float) ($durationDays * $car->price_per_day);
        $driverFee = $driverService ? (float) ($durationDays * self::DRIVER_DAILY_RATE) : 0.0;
        $totalPrice = $carRentalFee + $driverFee;

        return [
            'duration_days' => $durationDays,
            'car_rental_fee' => $carRentalFee,
            'driver_fee' => $driverFee,
            'total_price' => $totalPrice,
        ];
    }

    /**
     * Generate unique booking code.
     */
    public function generateBookingCode(): string
    {
        do {
            $code = 'BK-' . strtoupper(Str::random(8));
        } while (\App\Models\Booking::where('booking_code', $code)->exists());

        return $code;
    }
}
