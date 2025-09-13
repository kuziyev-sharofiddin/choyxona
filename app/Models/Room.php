<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_uz', 'capacity', 'daily_rate', 'description', 
        'amenities', 'status', 'image'
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'amenities' => 'array',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Kunlik asosda xonaning mavjudligini tekshirish
    public function isAvailableForDate($date, $daysCount = 1)
    {
        $startDate = Carbon::parse($date)->format('Y-m-d');
        $endDate = Carbon::parse($date)->addDays($daysCount - 1)->format('Y-m-d');
        
        return !$this->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('reservation_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('reservation_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })->exists();
    }

    // Bugungi aktiv rezervatsiyalar
    public function getCurrentReservation()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        return $this->reservations()
            ->where('status', 'confirmed')
            ->where('reservation_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();
    }

    // Xona holatini yangilash
    public function updateStatusBasedOnReservations()
    {
        $currentReservation = $this->getCurrentReservation();
        
        if ($currentReservation) {
            $this->update(['status' => 'occupied']);
        } else {
            $this->update(['status' => 'available']);
        }
    }

    // Band bo'lgan kunlarni olish (calendar uchun)
    public function getBookedDates($month = null, $year = null)
    {
        $query = $this->reservations()
            ->where('status', '!=', 'cancelled')
            ->select('reservation_date', 'end_date');

        if ($month && $year) {
            $query->whereYear('reservation_date', $year)
                  ->whereMonth('reservation_date', $month);
        }

        $bookedDates = [];
        foreach ($query->get() as $reservation) {
            $current = Carbon::parse($reservation->reservation_date);
            $end = Carbon::parse($reservation->end_date);
            
            while ($current->lte($end)) {
                $bookedDates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        return array_unique($bookedDates);
    }
}
