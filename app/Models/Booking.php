<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_DELIVERING = 'delivering';
    public const STATUS_IN_USE = 'in_use';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'booking_code',
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'duration_days',
        'driver_service',
        'delivery_address',
        'car_rental_fee',
        'driver_fee',
        'late_penalty_fee',
        'total_price',
        'status',
        'notes',
        // Delivery tracking fields
        'courier_name',
        'courier_phone',
        'dispatch_time',
        'estimated_duration_minutes',
        'estimated_arrival_time',
        'office_origin_address',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_days' => 'integer',
            'driver_service' => 'boolean',
            'car_rental_fee' => 'decimal:2',
            'driver_fee' => 'decimal:2',
            'late_penalty_fee' => 'decimal:2',
            'total_price' => 'decimal:2',
            'dispatch_time' => 'datetime',
            'estimated_arrival_time' => 'datetime',
            'estimated_duration_minutes' => 'integer',
        ];
    }

    /**
     * User who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Car booked.
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Check if currently in delivering phase.
     */
    public function isDelivering(): bool
    {
        return $this->status === self::STATUS_DELIVERING;
    }

    /**
     * Remaining ETA in minutes from now.
     */
    public function getRemainingMinutesAttribute(): int
    {
        if (!$this->estimated_arrival_time) {
            return (int) ($this->estimated_duration_minutes ?: 30);
        }

        $now = Carbon::now();
        if ($now->greaterThanOrEqualTo($this->estimated_arrival_time)) {
            return 0;
        }

        return max(1, (int) $now->diffInMinutes($this->estimated_arrival_time));
    }

    /**
     * Formatted Estimated Arrival Time.
     */
    public function getFormattedEtaAttribute(): string
    {
        if (!$this->estimated_arrival_time) {
            return '-';
        }

        return $this->estimated_arrival_time->translatedFormat('d M Y, H:i') . ' WIB';
    }

    /**
     * Formatted Dispatch Time.
     */
    public function getFormattedDispatchTimeAttribute(): string
    {
        if (!$this->dispatch_time) {
            return '-';
        }

        return $this->dispatch_time->translatedFormat('d M Y, H:i') . ' WIB';
    }

    /**
     * Formatted Office Origin Address.
     */
    public function getOriginAddressAttribute(): string
    {
        return $this->office_origin_address ?: 'Kantor Pusat AutoRent, Jl. Merdeka No. 1, Jakarta Pusat';
    }

    /**
     * Generate WhatsApp Direct Click URL to courier.
     */
    public function getCourierWhatsAppUrlAttribute(): ?string
    {
        if (!$this->courier_phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->courier_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $message = "Halo Pak " . ($this->courier_name ?: 'Driver') . ", saya pelanggan AutoRent untuk booking #" . $this->booking_code . " (" . $this->car?->brand . " " . $this->car?->model . "). Bagaimana posisi pengantaran armada mobil saat ini?";

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    /**
     * Human readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Konfirmasi',
            self::STATUS_APPROVED => 'Disetujui / Siap Dikirim',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_DELIVERING => 'Sedang Dikirim',
            self::STATUS_IN_USE => 'Sedang Digunakan',
            self::STATUS_RETURNED => 'Mobil Dikembalikan',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Tailwind CSS Badge colors.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::STATUS_APPROVED => 'bg-blue-100 text-blue-800 border-blue-300',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800 border-red-300',
            self::STATUS_DELIVERING => 'bg-purple-100 text-purple-800 border-purple-300',
            self::STATUS_IN_USE => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            self::STATUS_RETURNED => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            self::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::STATUS_CANCELLED => 'bg-gray-100 text-gray-800 border-gray-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    /**
     * Can customer cancel this booking?
     */
    public function canCancel(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
