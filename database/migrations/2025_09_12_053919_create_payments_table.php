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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('reservation_id')->nullable()
            ->constrained('reservations');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // cash, card, transfer
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->foreignId('cashier_id')->constrained('users');
            $table->timestamp('payment_time');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
