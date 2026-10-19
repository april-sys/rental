<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            [
                'brand' => 'Toyota',
                'model' => 'Avanza 1.5 G CVT',
                'license_plate' => 'B 1234 ABC',
                'capacity' => 7,
                'price_per_day' => 400000,
                'status' => 'available',
                'image' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80',
                'description' => 'MPV sejuta umat yang hemat bahan bakar, kabin lapang, AC digital double blower, dan suspensi nyaman untuk perjalanan keluarga maupun bisnis.',
            ],
            [
                'brand' => 'Toyota',
                'model' => 'Innova Reborn 2.4 V Diesel',
                'license_plate' => 'B 5678 DEF',
                'capacity' => 7,
                'price_per_day' => 650000,
                'status' => 'available',
                'image' => 'https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=800&q=80',
                'description' => 'Mobil keluarga premium bertenaga diesel torsi tinggi, kedap suara prima, captain seat nyaman, cocok untuk perjalanan dinas dan luar kota.',
            ],
            [
                'brand' => 'Toyota',
                'model' => 'Fortuner 2.8 VRZ GR Sport',
                'license_plate' => 'B 9999 GR',
                'capacity' => 7,
                'price_per_day' => 1100000,
                'status' => 'available',
                'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80',
                'description' => 'High SUV mewah dan tangguh, mesin 2800cc bertenaga besar, fitur keselamatan canggih TSS, audio JBL, dan tampilan gagah berkelas.',
            ],
            [
                'brand' => 'Honda',
                'model' => 'Brio RS 1.2 CVT',
                'license_plate' => 'B 3456 JKL',
                'capacity' => 5,
                'price_per_day' => 350000,
                'status' => 'available',
                'image' => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80',
                'description' => 'City car lincah, irit konsumsi bensin, desain sporty stylish, sangat fleksibel menembus kepadatan lalu lintas kota.',
            ],
            [
                'brand' => 'Mitsubishi',
                'model' => 'Pajero Sport Dakar 4x2 Ultimate',
                'license_plate' => 'B 7788 MNO',
                'capacity' => 7,
                'price_per_day' => 1150000,
                'status' => 'available',
                'image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80',
                'description' => 'SUV premium dengan sunroof, adaptive cruise control, transmisi 8 percepatan, dan kenyamanan suspensi kelas atas.',
            ],
        ];

        foreach ($cars as $car) {
            Car::updateOrCreate(
                ['license_plate' => $car['license_plate']],
                $car
            );
        }
    }
}
