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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('room_id')->constrained();
            $table->foreignId('user_id')->constrained(); // waiter
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->integer('guest_count');
            $table->decimal('room_charge', 10, 2);
            $table->text('special_requests')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, checked_in, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
