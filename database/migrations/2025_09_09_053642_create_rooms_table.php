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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_uz');
            $table->integer('capacity');
            $table->decimal('hourly_rate', 8, 2);
            $table->text('description')->nullable();
            $table->json('amenities')->nullable(); // TV, AC, etc
            $table->string('status')->default('available'); // available, occupied, maintenance
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
