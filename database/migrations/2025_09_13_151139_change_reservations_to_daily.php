<?php
// database/migrations/2024_12_01_000000_change_reservations_to_daily.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Rooms jadvalini o'zgartirish
        Schema::table('rooms', function (Blueprint $table) {
            $table->renameColumn('hourly_rate', 'daily_rate');
        });

        // 2. Reservations jadvalini o'zgartirish
        Schema::table('reservations', function (Blueprint $table) {
            // Yangi kunlik kolonlar qo'shish
            $table->date('reservation_date')->after('guest_count')->nullable();
            $table->integer('days_count')->default(1)->after('reservation_date');
            $table->date('end_date')->after('days_count')->nullable();
        });

        // 3. Mavjud ma'lumotlarni o'tkazish
        DB::table('reservations')->update([
            'reservation_date' => DB::raw('DATE(start_time)'),
            'end_date' => DB::raw('DATE(end_time)'),
            'days_count' => DB::raw('DATEDIFF(DATE(end_time), DATE(start_time)) + 1')
        ]);

        // 4. Eski kolonlarni olib tashlash
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });

        // 5. Xona narxlarini kunlikka o'tkazish (soatlik * 24)
        DB::table('rooms')->update([
            'daily_rate' => DB::raw('daily_rate * 24')
        ]);
    }

    public function down()
    {
        // Qaytarish uchun
        Schema::table('rooms', function (Blueprint $table) {
            $table->renameColumn('daily_rate', 'hourly_rate');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dateTime('start_time')->after('guest_count');
            $table->dateTime('end_time')->after('start_time');
            $table->dropColumn(['reservation_date', 'days_count', 'end_date']);
        });

        DB::table('rooms')->update([
            'hourly_rate' => DB::raw('hourly_rate / 24')
        ]);
    }
};