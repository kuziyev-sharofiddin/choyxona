<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'room_id', 'user_id', 'reservation_date', 
        'end_date', 'days_count', 'guest_count', 'room_charge', 
        'special_requests', 'status', 'reservation_number'
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'end_date' => 'date',
        'room_charge' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($reservation) {
            // Avtomatik rezervatsiya raqamini generatsiya qilish
            if (!$reservation->reservation_number) {
                $reservation->reservation_number = 'RES-' . date('Ymd') . '-' . str_pad(
                    (static::whereDate('created_at', today())->count() + 1), 
                    3, '0', STR_PAD_LEFT
                );
            }
            
            // End_date ni avtomatik hisoblash
            if ($reservation->reservation_date && $reservation->days_count) {
                $reservation->end_date = Carbon::parse($reservation->reservation_date)
                    ->addDays($reservation->days_count - 1)
                    ->format('Y-m-d');
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Jami summani hisoblash
    public function getTotalAmountAttribute()
    {
        $orderTotal = $this->orders()->sum('total_amount');
        return $this->room_charge + $orderTotal;
    }

    public function getTotalAmount()
    {
        $orderTotal = $this->orders()->sum('total_amount');
        return $this->room_charge + $orderTotal;
    }

    // Qolgan summani hisoblash
    public function getRemainingAmountAttribute()
    {
        $paidAmount = $this->payments()->where('status', 'completed')->sum('amount');
        return $this->total_amount - $paidAmount;
    }

    // To'lov holatini tekshirish
    public function getPaymentStatusAttribute()
    {
        $totalAmount = $this->total_amount;
        $paidAmount = $this->payments()->where('status', 'completed')->sum('amount');
        
        if ($paidAmount == 0) {
            return 'pending';
        } elseif ($paidAmount >= $totalAmount) {
            return 'paid';
        } else {
            return 'partial';
        }
    }

    // Rezervatsiya muddati o'tgan-o'tmaganini tekshirish
    public function getIsExpiredAttribute()
    {
        return $this->end_date->isPast() && $this->status !== 'completed';
    }

    // Rezervatsiya aktiv-aktiv emasligini tekshirish
    public function getIsActiveAttribute()
    {
        $today = Carbon::today();
        return $this->reservation_date->lte($today) && 
               $this->end_date->gte($today) && 
               $this->status === 'confirmed';
    }

    // Bugun boshlanadigan rezervatsiyalar
    public function scopeStartingToday($query)
    {
        return $query->where('reservation_date', today());
    }

    // Bugun tugaydigan rezervatsiyalar
    public function scopeEndingToday($query)
    {
        return $query->where('end_date', today());
    }

    // Ma'lum sanada aktiv rezervatsiyalar
    public function scopeActiveOnDate($query, $date)
    {
        return $query->where('reservation_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->where('status', 'confirmed');
    }

    // Kunlar farqini hisoblash
    public function getDuration()
    {
        return $this->days_count . ' kun';
    }
}
