<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'license_plate',
        'capacity',
        'price_per_day',
        'status',
        'image',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'price_per_day' => 'decimal:2',
        ];
    }

    /**
     * Bookings associated with this car.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope query to only available cars.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Check if car is available for a given date range.
     * Overlapping condition: (booking.start_date <= requested.end_date) AND (booking.end_date >= requested.start_date)
     */
    public function isAvailableForDates(string $startDate, string $endDate, ?int $excludeBookingId = null): bool
    {
        if ($this->status === 'maintenance') {
            return false;
        }

        $query = $this->bookings()
            ->whereIn('status', ['approved', 'delivering', 'in_use'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('start_date', '<=', $endDate)
                  ->where('end_date', '>=', $startDate);
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }

    /**
     * Get car full name (e.g. Toyota Avanza)
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model}";
    }

    /**
     * Formatted rental price per day.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_day, 0, ',', '.');
    }

    /**
     * Get image URL with fallback to custom default car image.
     */
    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        return asset('images/default-car.png');
    }
}
