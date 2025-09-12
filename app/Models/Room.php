<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_uz', 'capacity', 'hourly_rate', 'description', 
        'amenities', 'status', 'image'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'amenities' => 'array',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function currentReservation()
    {
        return $this->hasOne(Reservation::class)
                   ->where('status', 'checked_in')
                   ->where('start_time', '<=', now())
                   ->where('end_time', '>=', now());
    }

    public function isAvailable($startTime, $endTime)
    {
        return !$this->reservations()
                    ->where('status', '!=', 'cancelled')
                    ->where(function($query) use ($startTime, $endTime) {
                        $query->whereBetween('start_time', [$startTime, $endTime])
                              ->orWhereBetween('end_time', [$startTime, $endTime])
                              ->orWhere(function($q) use ($startTime, $endTime) {
                                  $q->where('start_time', '<=', $startTime)
                                    ->where('end_time', '>=', $endTime);
                              });
                    })->exists();
    }
}
