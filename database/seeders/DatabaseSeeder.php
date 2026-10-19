<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator Rental',
                'password' => Hash::make('password'),
                'phone_number' => '081234567890',
                'address' => 'Kantor Pusat AutoRent, Jl. Merdeka No. 1, Jakarta Pusat',
                'nik_ktp' => '3171012345670001',
                'role' => 'admin',
            ]
        );

        // 2. Seed Customer User
        $customer = User::updateOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'phone_number' => '081987654321',
                'address' => 'Jl. Sudirman Kav. 25, Apartemen Senayan City, Jakarta Selatan',
                'nik_ktp' => '3174098765430002',
                'role' => 'customer',
            ]
        );

        // 3. Seed Cars
        $this->call(CarSeeder::class);

        // 4. Sample Booking 1: Status "On Delivery" (Grab-style live tracking demo)
        $innova = Car::where('license_plate', 'B 5678 DEF')->first();
        if ($innova && $customer) {
            $innova->update(['status' => 'rented']);

            Booking::updateOrCreate(
                ['booking_code' => 'BK-DELIVERY1'],
                [
                    'user_id' => $customer->id,
                    'car_id' => $innova->id,
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addDays(2)->toDateString(),
                    'duration_days' => 2,
                    'driver_service' => true,
                    'delivery_address' => 'Jl. Sudirman Kav. 25, Apartemen Senayan City Tower A No. 1202, Jakarta Selatan',
                    'car_rental_fee' => 1300000,
                    'driver_fee' => 300000,
                    'late_penalty_fee' => 0,
                    'total_price' => 1600000,
                    'status' => Booking::STATUS_DELIVERING,
                    'notes' => 'Harap konfirmasi satpam lobi saat tiba di lobi tower A.',
                    'courier_name' => 'Bambang Sudiro (Driver AutoRent)',
                    'courier_phone' => '081298765432',
                    'dispatch_time' => now()->subMinutes(10),
                    'estimated_duration_minutes' => 35,
                    'estimated_arrival_time' => now()->addMinutes(25),
                    'office_origin_address' => 'Kantor Pusat AutoRent, Jl. Merdeka No. 1, Gambir, Jakarta Pusat',
                ]
            );
        }

        // 5. Sample Booking 2: Status "Pending" (For testing Admin approval & dispatch flow)
        $fortuner = Car::where('license_plate', 'B 9999 GR')->first();
        if ($fortuner && $customer) {
            Booking::updateOrCreate(
                ['booking_code' => 'BK-PENDING02'],
                [
                    'user_id' => $customer->id,
                    'car_id' => $fortuner->id,
                    'start_date' => now()->addDays(1)->toDateString(),
                    'end_date' => now()->addDays(3)->toDateString(),
                    'duration_days' => 2,
                    'driver_service' => false,
                    'delivery_address' => 'Jl. Gatot Subroto No. 45, Kuningan Barat, Jakarta Selatan',
                    'car_rental_fee' => 2200000,
                    'driver_fee' => 0,
                    'late_penalty_fee' => 0,
                    'total_price' => 2200000,
                    'status' => Booking::STATUS_PENDING,
                    'notes' => 'Pengantaran mohon dilakukan tepat pukul 08:00 pagi.',
                ]
            );
        }
    }
}
