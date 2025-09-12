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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('reservation_id')
              ->constrained('reservations') // jadval nomini to‘g‘ri yozish kerak
              ->onDelete('cascade');
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('waiter_id')->constrained('users');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('status')->default('pending'); // pending, preparing, ready, served, completed
            $table->text('notes')->nullable();
            $table->timestamp('order_time');
            $table->timestamp('served_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
