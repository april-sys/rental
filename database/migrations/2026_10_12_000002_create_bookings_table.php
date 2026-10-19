<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('duration_days');
            $table->boolean('driver_service')->default(false);
            $table->text('delivery_address');
            $table->decimal('car_rental_fee', 12, 2);
            $table->decimal('driver_fee', 12, 2)->default(0);
            $table->decimal('late_penalty_fee', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'delivering',
                'in_use',
                'returned',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
