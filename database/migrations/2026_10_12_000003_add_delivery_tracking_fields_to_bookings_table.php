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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('courier_name')->nullable()->after('notes');
            $table->string('courier_phone')->nullable()->after('courier_name');
            $table->timestamp('dispatch_time')->nullable()->after('courier_phone');
            $table->unsignedInteger('estimated_duration_minutes')->default(30)->after('dispatch_time');
            $table->timestamp('estimated_arrival_time')->nullable()->after('estimated_duration_minutes');
            $table->text('office_origin_address')->nullable()->after('estimated_arrival_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'courier_name',
                'courier_phone',
                'dispatch_time',
                'estimated_duration_minutes',
                'estimated_arrival_time',
                'office_origin_address',
            ]);
        });
    }
};
