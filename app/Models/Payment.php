<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number', 'reservation_id','order_id', 'amount', 'payment_method',
        'status', 'cashier_id', 'payment_time', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            $payment->payment_number = 'PAY-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        });
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    // Get customer - either from reservation or order
    public function getCustomer()
    {
        if ($this->reservation_id) {
            return $this->reservation->customer;
        } elseif ($this->order_id) {
            return $this->order->customer;
        }
        return null;
    }

    // Get payment source description
    public function getSourceDescription()
    {
        if ($this->reservation_id) {
            return 'Rezervatsiya #' . $this->reservation->reservation_number;
        } elseif ($this->order_id) {
            return 'Buyurtma #' . $this->order->order_number;
        }
        return 'Noma\'lum';
    }
}
